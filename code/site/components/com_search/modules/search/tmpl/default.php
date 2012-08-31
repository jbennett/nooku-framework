<?
/**
 * @version		$Id: form.php 1294 2011-05-16 22:57:57Z johanjanssens $
 * @package     Nooku_Server
 * @subpackage  Search
 * @copyright	Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */
?>

<form action="<?= @route('option=com_search'); ?>" method="get">

<div class="control-group">
	<div class="controls">
	<? $output = '<input name="term" id="mod_search_term" maxlength="' . $maxlength . '" alt="' . $button_text . '" class="inputbox" type="text" size="' . $width . '" value="' . $text . '"  onblur="if(this.value==\'\') this.value=\'' . $text . '\';" onfocus="if(this.value==\'' . $text . '\') this.value=\'\';" />';
						
	    if($button) :
			$button = '<input type="submit" value="' . $button_text . '" onclick="this.form.searchword.focus();"/>';
		endif;
						
		switch($button_pos) :
						
		    case 'top':
			    $button = $button . '<br />';
				$output = $button . $output;
				break;
							
			case 'bottom':
				$button = '<br />' . $button;
				$output = $output . $button;
				break;
							
			case 'right':
				$output = $output . $button;
				break;
							
			case 'left':
			default:
				$output = $button . $output;
				break;
			
		endswitch;
						
		echo $output;
	?>
	</div>
</div>
</form>