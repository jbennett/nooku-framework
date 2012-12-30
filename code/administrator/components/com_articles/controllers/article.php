<?php
/**
 * @version		$Id$
 * @package     Nooku_Server
 * @subpackage  Articles
 * @copyright	Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Article Controller Class
 *
 * @author    	Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Articles
 */
class ComArticlesControllerArticle extends ComDefaultControllerModel
{ 
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'behaviors' => array(
    	        'com://admin/activities.controller.behavior.loggable',
    	        'com://admin/versions.controller.behavior.revisable',
    		    'com://admin/languages.controller.behavior.translatable',
    	        //'cacheable'
    	    )
    	));
    
    	parent::_initialize($config);
    }
}