<?php
/**
 * @package     Nooku_Server
 * @subpackage  Pages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Menus Model Class
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Pages
 */
class ComPagesModelMenus extends KModelTable
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('sort', 'cmd', 'title')
            ->insert('application', 'word');
    }

    protected function _buildQueryColumns(KDatabaseQuerySelect $query)
    {
        parent::_buildQueryColumns($query);

        if(!$query->isCountQuery()) {
            $query->columns(array('page_count' => 'COUNT(pages.pages_page_id)'));
        }
    }

    protected function _buildQueryJoins(KDatabaseQuerySelect $query)
    {
        parent::_buildQueryJoins($query);

        if(!$query->isCountQuery()) {
            $query->join(array('pages' => 'pages'), 'tbl.pages_menu_id = pages.pages_menu_id');
        }
    }

    protected function _buildQueryGroup(KDatabaseQuerySelect $query)
    {
        parent::_buildQueryGroup($query);

        if(!$query->isCountQuery()) {
            $query->group('pages.pages_menu_id');
        }
    }

    protected function _buildQueryWhere(KDatabaseQuerySelect $query)
    {
        parent::_buildQueryWhere($query);
        $state = $this->getState();

        if(!$state->isUnique())
        {
            if($state->search)
            {
                $query->where('tbl.title LIKE :search')
                    ->bind(array('search' => '%'.$state->search.'%'));;
            }
            
            if($state->application) {
                $query->where('tbl.application = :application')->bind(array('application' => $state->application));
            }
        }
    }
}