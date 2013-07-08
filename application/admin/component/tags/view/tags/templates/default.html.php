<?
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */
?>

<ktml:module position="toolbar">
    <?= @helper('toolbar.render', array('toolbar' => $toolbar))?>
</ktml:module>

<form action="" method="post" class="-koowa-grid">
    <?= @template('default_scopebar.html'); ?>
    <table>
        <thead>
            <tr>
                <th width="10">
                    <?= @helper('grid.checkall'); ?>
                </th>
                <th>
                    <?= @helper('grid.sort', array('column' => 'title')); ?>
                </th>
                <th>
                    <?= @helper('grid.sort', array('column' => 'count')); ?>
                </th>
            </tr>
        </thead>
        
        <tfoot>
            <tr>
                <td colspan="4">
                    <?= @helper('com:application.paginator.pagination', array('total' => $total)) ?>
                </td>
            </tr>
        </tfoot>
        
        <tbody>        
            <? foreach ($tags as $tag) : ?>
            <tr>
                <td align="center">
                    <?= @helper('grid.checkbox', array('row' => $tag)); ?>
                </td>
                <td>
<<<<<<< HEAD:application/admin/component/terms/view/terms/templates/default.html.php
                    <a href="<?= @route('view=term&id='.$term->id); ?>">
                        <?= @escape($term->title); ?>
=======
                    <a href="<?= @route('view=tag&id='.$tag->id); ?>">
                        <?= @escape($tag->title); ?>
>>>>>>> develop:application/admin/component/tags/view/tags/templates/default.html.php
                    </a>
                </td>
                <td>
                    <?= @escape($tag->count); ?>
                </td>
            </tr>
            <? endforeach; ?>	
            <? if (!count($tags)) : ?>
            <tr>
                <td colspan="4" align="center">
                    <?= @text('No items found'); ?>
                </td>
            </tr>
            <? endif; ?>
        </tbody>
    </table>
</form>