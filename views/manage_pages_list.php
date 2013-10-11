<?php
if (!empty($pages[$current_parent_id])) {
	$curr_menu_id = 'page-list-'.$current_parent_id;
?>
<div class="page-list-content">
	<dl id="<?php echo $curr_menu_id ?>" class="page-manager-list">
	<?php
		foreach ($pages[$current_parent_id] as $page) {
			$show_children = (!empty($pages[$page->id()]) && $with_children);
			$extra_classname = '';
			if ($show_children) {
				$extra_classname = ' has-children';
			}
			?><dd id="list-page_<?php echo $page->id() ?>" class="<?php echo $Navigation->tiger_stripe('manage-pages-list-'.$top_level_parent_id); echo $extra_classname ?>"><div class="page-item-container"><?php
			if ($PageContentManager->user_can_edit($page) || $PageContentManager->user_can_delete($page)) {
				?><div class="controls"><?php
				if ($PageContentManager->user_can_delete($page)) {
					$extra_delete_warning = '';
					?><a href="<?php echo $PageContentManager->url('delete',$page->id()) ?>" class="btn-right delete-button" data-item-type="<?php echo __('Page'); ?>" data-item-title="<?php echo Crumbs::entitize_utf8(H::purify_text($page->navigation_title())); ?>"<?php
					if ($page->has_children()) {
						?> data-additional-text="<?php echo __('<strong>WARNING:</strong> Deleting this page will also remove all of it\'s child pages.'); ?>"<?php
					} ?>><?php echo __('Delete'); ?></a><?php
				}
				if ($PageContentManager->user_can_create() && $page->slug() != 'index') {
					?><a href="<?php echo $PageContentManager->url('new') ?>?page_defaults[parent]=<?php echo $page->id(); ?>&amp;return_url=<?php echo $PageContentManager->url('manage_pages') ?>" class="btn-right new-button"><?php echo __('New Sub-Page'); ?></a><?php
				}
				if ($PageContentManager->user_can_edit($page)) {
					?><a href="<?php echo $PageContentManager->url('edit',$page->id()) ?>?return_url=<?php echo $PageContentManager->url('manage_pages') ?>" class="btn-right edit-button"><?php echo __('Edit'); ?></a><?php
				}
				?></div><?php
			}
			?><div id="drag-handle-<?php echo $page->id() ?>" class="drag-handle ui-icon" style="display: none"><?php echo __('Drag to Sort'); ?></div><div class="page-link-container"><a class="page-link" href="<?php echo $page->url() ?>"><?php
			echo $page->navigation_title();
			?></a><?php
			if ($page->slug() == 'index') {
				?> <span class="small">(<?php echo __('Home Page'); ?>)</span><?php
			} else if ($page->access_level() > PUBLIC_USER) {
				?> <span class="small">(<?php echo sprintf(__('%s access required'),$page->access_level_name()); ?>)</span><?php
			}
			if ($page->ext_link()) {
				?> <span class="small notice" style="padding: 1px 5px"><?php echo __('Redirect'); ?></span><?php
			}
			?></div><div class="clearance"></div></div><?php
			if ($show_children) {
				echo $Navigation->render_pages_hierarchically($pages, $page->id(), $with_children, $view_file,array('top_level' => false, 'top_level_parent_id' => $top_level_parent_id));
			}
			?></dd><?php
		}
		?>
	</dl>
</div>
<?php
}
?>
