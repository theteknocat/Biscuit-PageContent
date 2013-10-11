<?php
if (!empty($pages[$current_parent_id])) {
	foreach ($pages[$current_parent_id] as $page) {
		if ($page->id() != $current_page_id) {
			$slug_bits = explode('/',$page->slug());
			$indent = count($slug_bits);
			$indent_str = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',$indent);
			$page_title = $page->title();
			$page_title = $indent_str.H::purify_text($page->title());
			if ($page->access_level() > PUBLIC_USER) {
				$page_title .= ' ('.$page->access_level_name().' access required)';
			}
			?><option value="<?php echo $page->id() ?>"<?php if ($page->id() == $selected_parent) { ?> selected="selected"<?php } ?>><?php echo $page_title; ?></option><?php
			if (!empty($pages[$page->id()]) && $with_children) {
				echo $Navigation->render_pages_hierarchically($pages, $page->id(), $with_children, $view_file, array('current_page_id' => $current_page_id, 'selected_parent' => $selected_parent));
			}
		}
	}
}
?>