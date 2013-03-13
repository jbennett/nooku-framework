<?php
/**
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Users
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Group Users Model Class
 *
 * @author      Tom Janssens <http://nooku.assembla.com/profile/tomjanssens>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Users
 */
class UsersModelGroups_users extends Framework\ModelTable
{
	protected function _buildQueryColumns(Framework\DatabaseQuerySelect $query)
	{
		parent::_buildQueryColumns($query);
	
		$query->columns(array(
			'group_name'    => 'group.name'
		));
	}
	
	protected function _buildQueryJoins(Framework\DatabaseQuerySelect $query)
	{
		$query->join(array('group' => 'users_groups'), 'group.users_group_id = tbl.users_group_id');
	}
	
	protected function _buildQueryWhere(Framework\DatabaseQuerySelect $query)
	{
	    parent::_buildQueryWhere($query);
		$state = $this->getState();
		
		if ($user_id = $state->user_id) {
			$query->where('tbl.users_user_id = :user_id')->bind(array('user_id' => $user_id));
		}
		
		if ($group_id = $state->group_id) {
			$query->where('tbl.users_group_id = :group_id')->bind(array('group_id' => $group_id));
		}
	}
}