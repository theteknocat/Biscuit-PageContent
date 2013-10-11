<?php
if (!empty($pages[$current_parent_id])) {
	$curr_menu_id = 'page-list-'.$current_parent_id;
?>
<div class="page-list-content"><img src="/modules/page_content/images/ajax_throbber.gif" style="display: none" id="page-list-throbber-<?php echo $current_parent_id ?>" class="page-list-throbber">
	<dl id="<?php echo $curr_menu_id ?>" class="page-manager-list">
	<?php
		foreach ($pages[$current_parent_id] as $page) {
			$has_children = (!empty($pages[$page->id()]) && $with_children);
			$extra_classname = '';
			if ($has_children) {
				$extra_classname = ' has-children';
			}
			?><dd id="list-page_<?php echo $page->id() ?>" class="<?php echo $Navigation->tiger_stripe('manage-pages-list-'.$top_level_parent_id); echo $extra_classname ?>"><div class="page-item-container"><?php
			if ($PageContentManager->user_can_edit($page) || $PageContentManager->user_can_delete($page)) {
				?><div class="controls"><?php
				if ($PageContentManager->user_can_delete($page)) {
					$extra_delete_warning = '';
					if ($page->has_children()) {
						$extra_delete_warning = '|WARNING: Deleting this page will also remove all of it\'s child pages.';
					}
					?><a style="margin: 0 0 0 5px" href="<?php echo $PageContentManager->url('delete',$page->id()) ?>" class="delete-button" rel="Page|<?php echo htmlentities($page->title()); echo $extra_delete_warning; ?>">Delete</a><?php
				}
				if ($PageContentManager->user_can_edit($page)) {
					?><a style="margin: 0 0 0 5px" href="<?php echo $PageContentManager->url('edit',$page->id()) ?>?return_url=<?php echo $PageContentManager->url('manage_pages') ?>">Edit</a><?php
				}
				?></div><?php
			}
			?><div id="drag-handle-<?php echo $page->id() ?>" class="draggable" style="display: none">Drag</div><div class="page-link-container"><a class="page-link" href="<?php echo $page->url() ?>"><?php
			echo $page->navigation_title();
			?></a><?php
			if ($page->slug() == 'index') {
				?> <span class="small">(Home Page)</span><?php
			} else if ($page->access_level() > PUBLIC_USER) {
				?> <span class="small">(<?php echo $page->access_level_name() ?> access required)</span><?php
			}
			?></div></div><?php
			if (!empty($pages[$page->id()]) && $with_children) {
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
