<?php
if ($Biscuit->module_exists('FileManager')) {
	$custom_buttons = array(
		array('href' => '#file-manager', 'label' => 'Manage Files', 'id' => 'file-manager-button', 'classname' => 'file-button'),
	);
} else {
	$custom_buttons = array(
		array('href' => '#file-manager', 'label' => 'Manage Files', 'id' => 'file-manager-button', 'classname' => 'file-button'),
		array('href' => '#image-manager', 'label' => 'Manage Images', 'id' => 'image-manager-button', 'classname' => 'image-button')
	);
}
print $Navigation->render_admin_bar($PageContentManager,NULL,array(
	'has_new_button' => true,
	'new_button_label' => 'New Page',
	'custom_buttons' => $custom_buttons
));
?>
<p>Following are all the pages you can modify using the Page Content Manager module. Any pages not listed here are not editable.</p>
<p><strong>Note:</strong> After sorting pages by drag-and-drop, the site menus will not reflect the change until you reload the page.</p>
<noscript>
	<p>You must enable Javascript in order to sort pages by drag-and-drop.</p>
</noscript>
<?php echo $page_list ?>
<script type="text/javascript">
	<?php
	$token_info = RequestTokens::get();
	?>
	var sortable_request_token = '<?php echo $token_info['token']; ?>';
	var sortable_token_form_id = '<?php echo $token_info['form_id']; ?>';
	$(document).ready(function() {
			<?php
			if ($Biscuit->module_exists('FileManager')) {
				?>
		$('#file-manager-button').click(function() {
			FileManagerActivate.standalone();
			return false;
		});
				<?php
			} else {
				?>
		$('#file-manager-button').click(function() {
			tinyBrowserPopUp('file',null);
			return false;
		});
		$('#image-manager-button').click(function() {
			tinyBrowserPopUp('image',null);
			return false;
		});
				<?php
			}
			?>
		$('.page-list-container').each(function() {
			var my_id = $(this).attr('id').substr(20);	// Everything after "page-list-container-"
			var top_level_parent_id = "page-list-"+my_id;
			$(this).find('dl.page-manager-list').each(function() {
				if ($(this).children('dd').length > 1) {
					$(this).children('dd').children('.page-item-container').children('.drag-handle').show();
					Biscuit.Crumbs.Sortable.create(this,'/content_editor',{
						handle: '.drag-handle',
						array_name: 'page_sort',
						axis: 'y',
						onUpdate: function() {
							Biscuit.Crumbs.ShowCoverThrobber(top_level_parent_id, 'Saving...');
							PageContent.RestripePageList(top_level_parent_id);
						},
						onFinish: function(list_id) {
							Biscuit.Crumbs.HideCoverThrobber(top_level_parent_id);
						}
					});
				}
			});
		});
	});
</script>
