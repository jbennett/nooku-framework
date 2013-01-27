<?php
class ComPagesDatabaseBehaviorOrderableClosure extends ComPagesDatabaseBehaviorOrderableAbstract implements ComPagesDatabaseBehaviorOrderableInterface
{
    protected $_columns = array();

    protected $_table;

    protected $_old_row;
    
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if($config->columns) {
            $this->_columns = KConfig::unbox($config->columns);
        }

        if($config->table) {
            $this->_table = $config->table;
        }
    }
    
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'   => KCommand::PRIORITY_LOWEST,
            'auto_mixin' => true,
            'columns'    => array(),
            'table'      => null
        ));

        parent::_initialize($config);
    }
    
    public function getOrderingTable()
    {
        if(!$this->_table instanceof KDatabaseTableInterface)
        {
            $table = $this->getMixer() instanceof KDatabaseTableInterface ? $this : $this->getTable();
            $this->_table = $this->getService($this->_table, array('identity_column' => $table->getIdentityColumn()));
        }
        
        return $this->_table;
    }
    
    protected function _buildQuery(KDatabaseRowInterface $row)
    {
        $table = $row->getTable();
        $query = $this->getService('koowa:database.query.select')
            ->table(array('tbl' => $table->getName()))
            ->join(array('crumbs' => $table->getClosureTable()->getName()), 'crumbs.descendant_id = tbl.'.$table->getIdentityColumn(), 'INNER')
            ->join(array('closures' => $table->getClosureTable()->getName()), 'closures.descendant_id = tbl.'.$table->getIdentityColumn(), 'INNER')
            
            ->group('tbl.'.$table->getIdentityColumn())
            ->having('COUNT(`crumbs`.`ancestor_id`) = :level')
            ->bind(array('level' => $row->level));
        
        if($row->level > 1) {
            $query->where('closures.ancestor_id = :ancestor_id')->bind(array('ancestor_id' => $row->getParentId()));
        }

        // Custom
        $query->where('tbl.pages_menu_id = :pages_menu_id')->bind(array('pages_menu_id' => $row->pages_menu_id));

        return $query;
    }
    
    protected function _beforeTableSelect(KCommandContext $context)
    {
        if($query = $context->query)
        {
            // Calculate ordering_path only if querying a list and it's sorted by an ordering column.
            $state = $context->options->state;
            if(!$query->isCountQuery() && $state && !$state->isUnique())
            {
                if(in_array($state->sort, $this->_columns))
                {
                    $table = $context->getSubject();
                    $query->columns(array('ordering_path' => 'GROUP_CONCAT(ordering_crumbs.'.$state->sort.' ORDER BY crumbs.level DESC  SEPARATOR \'/\')'))
                        ->join(array('ordering_crumbs' => $table->getOrderingTable()->getName()), 'crumbs.ancestor_id = ordering_crumbs.'.$table->getIdentityColumn(), 'INNER');

                    // Replace sort column with ordering path.
                    foreach($query->order as &$order)
                    {
                        if($order['column'] == $state->sort)
                        {
                            $order['column'] = 'ordering_path';
                            break;
                        }
                    }
                }

                $query->columns(array('ordering' => 'CAST(SUBSTRING_INDEX(GROUP_CONCAT(ordering_crumbs.custom ORDER BY crumbs.level DESC  SEPARATOR \'/\'), \'/\', -1) AS UNSIGNED)'));
            }
        }
    }
    
    protected function _afterTableInsert(KCommandContext $context)
    {
        $row = $context->data;
        if($row->getStatus() != KDatabase::STATUS_FAILED)
        {
            // Insert empty row into ordering table.
            $table = $row->getTable();
            $empty = $table->getOrderingTable()->getRow()->setData(array('id' => $row->id));
            $table->getOrderingTable()->insert($empty);
            
            // Iterate through the columns and update values.
            foreach($this->_columns as $column) {
                call_user_func(array($this, '_reorder'.ucfirst($column)), $row, $column, $context->operation);
            }
        }
    }

    protected function _beforeTableUpdate(KCommandContext $context)
    {
        $row = $context->data;
        if($row->isModified('parent_id')) {
            $this->_old_row = $row->getTable()->select($row->id, KDatabase::FETCH_ROW);
        }
    }
    
    protected function _afterTableUpdate(KCommandContext $context)
    {
        $row = $context->data;
        if($row->getStatus() != KDatabase::STATUS_FAILED)
        {
            foreach($this->_columns as $column) {
                call_user_func(array($this, '_reorder'.ucfirst($column)), $row, $column, $context->operation);
            }

            // If parent has changed, update old tree.
            if(isset($this->_old_row) && $row->parent_id != $this->_old_row->parent_id)
            {
                foreach($this->_columns as $column) {
                    call_user_func(array($this, '_reorder'.ucfirst($column)), $this->_old_row, $column, $context->operation);
                }
            }
        }
    }
    
    protected function _afterTableDelete(KCommandContext $context)
    {
        $row = $context->data;
        if($row->getStatus() != KDatabase::STATUS_FAILED)
        {
            foreach($this->_columns as $column) {
                call_user_func(array($this, '_reorder'.ucfirst($column)), $row, $column, $context->operation);
            }
        }
    }
    
    protected function _reorderDefault(KDatabaseRowInterface $row, $column, $operation)
    {
        $table = $row->getTable();

        // Create a select query which returns an ordered list of rows.
        $table->getDatabase()->execute('SET @index := 0');
        
        $sub_select = $this->_buildQuery($row)
            ->columns('tbl.'.$column)
            ->columns('tbl.'.$table->getIdentityColumn())
            ->order('tbl.'.$column, 'ASC');
        
        $select = $this->getService('koowa:database.query.select')
            ->columns(array('index' => '@index := @index + 1'))
            ->columns('tbl.*')
            ->table(array('tbl' => $sub_select));
        
        // Create a multi-table update query which uses the select query as join table.
        $update = $this->getService('koowa:database.query.update')
            ->table(array('tbl' => $table->getOrderingTable()->getBase()))
            ->join(array('ordering' => $select), 'tbl.'.$table->getIdentityColumn().' = ordering.'.$table->getIdentityColumn())
            ->values('tbl.'.$column.' = ordering.index')
            ->where('tbl.'.$table->getIdentityColumn().' = ordering.'.$table->getIdentityColumn());

        $table->getDatabase()->update($update);
    }

    protected function _reorderCustom(KDatabaseRowInterface $row, $column, $operation)
    {
        $table = $row->getTable();

        switch($operation)
        {
            case KDatabase::OPERATION_INSERT:
            {
                $query = $this->_buildQuery($row)
                    ->columns('orderings.custom')
                    ->join(array('orderings' => $table->getOrderingTable()->getName()), 'tbl.'.$table->getIdentityColumn().' = orderings.'.$table->getIdentityColumn(), 'INNER')
                    ->order('orderings.custom', 'DESC')
                    ->limit(1);

                $max = (int) $table->getDatabase()->select($query, KDatabase::FETCH_FIELD);
                $table->getOrderingTable()->select($row->id, KDatabase::FETCH_ROW)
                    ->setData(array('custom' => $max + 1))->save();
            } break;

            case KDatabase::OPERATION_UPDATE:
            {
                if($row->order)
                {
                    $old = (int) $row->ordering;
                    $new = $row->ordering + $row->order;
                    $new = $new <= 0 ? 1 : $new;

                    $select = $this->_buildQuery($row)
                        ->columns('orderings.custom')
                        ->columns('tbl.'.$table->getIdentityColumn())
                        ->join(array('orderings' => $table->getOrderingTable()->getBase()), 'tbl.'.$table->getIdentityColumn().' = orderings.'.$table->getIdentityColumn(), 'INNER')
                        ->order('index', 'ASC');

                    if($row->order < 0)
                    {
                        $select->columns(array('index' => 'IF(orderings.custom >= :new AND orderings.custom < :old, orderings.custom + 1, '.
                            'IF(orderings.'.$table->getIdentityColumn().' = :id, :new, orderings.custom))'));
                    }
                    else
                    {
                        $select->columns(array('index' => 'IF(orderings.custom > :old AND orderings.custom <= :new, orderings.custom - 1, '.
                            'IF(orderings.'.$table->getIdentityColumn().' = :id, :new, orderings.custom))'));
                    }

                    $select->bind(array('new' => $new, 'old' => $old, 'id' => $row->id));

                    $update = $this->getService('koowa:database.query.update')
                        ->table(array('tbl' => $table->getOrderingTable()->getBase()))
                        ->join(array('ordering' => $select), 'tbl.'.$table->getIdentityColumn().' = ordering.'.$table->getIdentityColumn())
                        ->values('tbl.'.$column.' = ordering.index')
                        ->where('tbl.'.$table->getIdentityColumn().' = ordering.'.$table->getIdentityColumn());

                    $table->getDatabase()->update($update);
                }
            } break;

            case KDatabase::OPERATION_DELETE:
            {
                $table->getDatabase()->execute('SET @index := 0');

                $select = $this->_buildQuery($row)
                    ->columns(array('index' => '@index := @index + 1'))
                    ->columns('orderings.custom')
                    ->columns('tbl.'.$table->getIdentityColumn())
                    ->join(array('orderings' => $table->getOrderingTable()->getBase()), 'tbl.'.$table->getIdentityColumn().' = orderings.'.$table->getIdentityColumn(), 'INNER')
                    ->order('index', 'ASC');

                $update = $this->getService('koowa:database.query.update')
                    ->table(array('tbl' => $table->getOrderingTable()->getBase()))
                    ->join(array('ordering' => $select), 'tbl.'.$table->getIdentityColumn().' = ordering.'.$table->getIdentityColumn())
                    ->values('tbl.'.$column.' = ordering.index')
                    ->where('tbl.'.$table->getIdentityColumn().' = ordering.'.$table->getIdentityColumn());

                $table->getDatabase()->update($update);
            } break;
        }
    }

    public function __call($name, $arguments)
    {
        if(strpos($name, '_reorder') === 0) {
            $result = call_user_func_array(array($this, '_reorderDefault'), $arguments);
        } else {
            $result = parent::__call($name, $arguments);
        }

        return $result;
    }
}