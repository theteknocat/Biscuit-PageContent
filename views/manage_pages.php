<?php
print $Navigation->render_admin_bar($PageContentManager,NULL,array(
	'has_new_button' => true,
	'new_button_label' => 'New Page',
	'custom_buttons' => array(
		array('href' => '#file-manager', 'label' => 'Manage Files', 'id' => 'file-manager-button'),
		array('href' => '#image-manager', 'label' => 'Manage Images', 'id' => 'image-manager-button')
	)
));
?>
<p>Following are all the pages you can modify using the Page Content Manager module. Any pages not listed here are not editable.</p>
<p><strong>Note:</strong> After sorting pages by drag-and-drop, the site menus will not reflect the change until you reload the page.</p>
<noscript>
	<p>You must enable Javascript in order to sort pages by drag-and-drop.</p>
</noscript>
<?php echo $page_list ?>
<script type="text/javascript" charset="utf-8">
	document.observe('dom:loaded',function() {
		$('file-manager-button').observe('click',function(event) {
			Event.stop(event);
			tinyBrowserPopUp('file',null);
		});
		$('image-manager-button').observe('click',function(event) {
			Event.stop(event);
			tinyBrowserPopUp('image',null);
		});
	});
</script>