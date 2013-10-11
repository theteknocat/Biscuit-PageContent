<?php
	$allowed_html = "p[class|style],
					strong,
					b,
					i,
					em,
					h1[class|style],
					h2[class|style],
					h3[class|style],
					h4[class|style],
					br,
					hr,
					a[href|title|class|style|target|name],
					ul[class|style],
					ol[class|style],
					li[class|style],
					dl[class|style],
					dt[class|style],
					dd[class|style],
					span[class|style],
					img[alt|src|width|height|border|class|style],
					sup,
					sub,
					table[width|cellpadding|cellspacing|border|class|style],
					tr[class|style],
					td[width|align|valign|style|class]";
	$admin_buttons = array();
	if ($PageContentManager->user_can_create() && $page->slug() != 'index') {
		$admin_buttons[] = array('href' => $PageContentManager->url('new').'?page_defaults[parent]='.$page->id().'&amp;return_url='.rawurlencode(Request::uri()), 'label' => 'New Sub-Page', 'classname' => 'new-button');
	}
	if ($PageContentManager->user_can_manage_pages()) {
		$admin_buttons[] = array('href' => $PageContentManager->url('manage_pages'), 'label' => 'Manage Pages');
	}
	print $Navigation->render_admin_bar($PageContentManager,$page,array(
		'bar_title' => 'Page Administration',
		'has_edit_button' => $PageContentManager->user_can_edit($page),
		'edit_button_label' => 'Edit Content',
		'has_del_button' => $PageContentManager->user_can_delete($page),
		'del_button_label' => 'Delete this Page',
		'del_button_rel' => 'this page',
		'custom_buttons' => $admin_buttons
	));
	$fcache = new FragmentCache('Page',$page->id());
	if ($fcache->start('full-page-content')) {
		echo H::purify_html($page->content(),array('allowed' => $allowed_html));
		$fcache->end('full-page-content');
	}
?>