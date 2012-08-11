<?php
/**
 * @version     $Id: page.php 3216 2011-11-28 15:33:44Z kotuha $
 * @package     Nooku_Server
 * @subpackage  Pages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Pages Toolbar Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package     Nooku_Server
 * @subpackage  Pages
 */

class ComPagesControllerToolbarPage extends ComDefaultControllerToolbarDefault
{
    public function onAfterControllerBrowse(KEvent $event)
    {
        parent::onAfterControllerBrowse($event);

        $this->addSeparator()
             ->addPublish()
             ->addUnpublish()
             ->addSeparator()
             ->addDefault();
    }

    protected function _commandDefault(KControllerToolbarCommand $command)
    {
        $command->label = JText::_('Make Default');

        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{home:1}'
            )
        ));
    }

    protected function _commandRestore(KControllerToolbarCommand $command)
    {
        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
            )
        ));
    }

    protected function _commandPublish(KControllerToolbarCommand $command)
    {
        $command->append(array(
            'attribs'  => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:1}'
            )
        ));
    }

    protected function _commandUnpublish(KControllerToolbarCommand $command)
    {
        $command->append(array(
            'attribs'  => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:0}'
            )
        ));
    }

    protected function _commandNew(KControllerToolbarCommand $command)
    {
        $menu = $this->getController()->getModel()->menu;
        $command->attribs->href = JRoute::_('index.php?option=com_pages&view=page&menu='.$menu);
    }
}
