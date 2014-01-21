<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;
use Nooku\Component\Attachments;

/**
 * Attachment Controller
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Component\Attachments
 */
class AttachmentsControllerAttachment extends Attachments\ControllerAttachment
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'request' => array(
				'view' => 'attachments'
			),
            'behaviors' => array(
                'editable', 'persistable',
            )
		));
		
		parent::_initialize($config);
	}
}