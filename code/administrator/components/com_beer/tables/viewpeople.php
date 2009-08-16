<?php
/**
 * Business Enterprise Employee Repository (B.E.E.R)
 * @version		$Id$
 * @package		Beer
 * @copyright	Copyright (C) 2009 Nooku. All rights reserved.
 * @license 	GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.nooku.org
 */

/**
 * see viewdepartments.php comment
 */
class BeerTableViewpeople extends KDatabaseTableAbstract
{
	public function getPrimaryKey()
	{
		return 'beer_person_id';
	}
}