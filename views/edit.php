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
$date = new Date();
?>
<?php print Form::header($page) ?>
	<input type="hidden" name="page[updated]" value="<?php echo $date->format('Y-m-d H:i:s') ?>">
	<fieldset>
		<legend>Content</legend>
		<?php echo ModelForm::text($page,'title') ?>

		<?php echo ModelForm::textarea($page,'content', true, $allowed_html) ?>

	</fieldset>
	<fieldset class="meta-data">
		<legend>Meta Data (for search engines)</legend>
		<?php echo ModelForm::textarea($page,'description') ?>

		<?php echo ModelForm::textarea($page,'keywords') ?>

	</fieldset>
	<?php
	if ($page->slug() != "index" && $PageContentManager->user_can_manage_pages()) {
		?>
	<fieldset>
		<legend>Menu Properties</legend>
			<p class="<?php echo $Navigation->tiger_stripe('striped_Page_form') ?>">
				<label for="attr_parent">*Parent Menu:</label><select name="page[parent]" id="attr_parent">
				<?php echo $PageContentManager->render_parent_option_list($page->parent()) ?>
				</select>
				<span class="instructions">
					You can modify the sort order by drag-and-drop from the Manage Pages page after saving.
				</span>
			</p>
			<?php echo ModelForm::radios(array(
				array(
					'label' => 'Yes',
					'value' => 1
				),
				array(
					'label' => 'No',
					'value' => 0
				)), $page, 'exclude_from_nav', 'Choosing "Yes" will exclude the page from navigation menus, but not from breadcrumbs.') ?>
	</fieldset>
		<?php
	}
	if ($PageContentManager->user_can_manage_pages()) {
		?>
	<fieldset>
		<legend>Permissions</legend>
		<?php echo ModelForm::select($user_select_list,$page,'owner_id','You can change the owner of the page if you wish to give another user permission to edit the page\'s content in addition to yourself.  Note that only users with permission to manage all pages can set this permission, modify a page\'s menu properties and access the file manager.') ?>

		<?php
		if ($page->access_level() <= $Authenticator->active_user()->user_level()) {
			echo ModelForm::select($access_select_list,$page,'access_level','This defines what level of access is required to view the page content.');
		}
		?>

	</fieldset>
		<?php
	}
	?>
	<?php echo Form::footer($PageContentManager, $page, $PageContentManager->user_can_delete($page), 'Save', $PageContentManager->return_url('Page'), 'this page') ?>
<script type="text/javascript" charset="utf-8">
	document.observe("dom:loaded",function() {
		PageContent.AddEditHandlers();
		Biscuit.Session.KeepAlive.init_form_observer();
	});
	tinyMCE.init({
		mode : "exact",
		elements: "attr_content",
		theme: 'advanced',
		theme_advanced_buttons1: 'undo,redo,|,pasteword,pastetext,|,search,replace,|,justifyleft,justifycenter,justifyright,justifyfull,|,indent,outdent,|,bullist,numlist,|,hr,|,anchor,link,unlink,image,|,charmap<?php if ($Authenticator->user_is_super()) { ?>,|,code<?php } ?>',
		theme_advanced_buttons2: 'bold,italic,underline,|,sup,sub,styleselect,formatselect,removeformat',
		theme_advanced_buttons3: 'table,tablecontrols',
		theme_advanced_buttons4: null,
		theme_advanced_buttons5: null,
		theme_advanced_buttons6: null,
		theme_advanced_toolbar_align: 'left',
		theme_advanced_toolbar_location: 'top',
		theme_advanced_resizing: true,
		theme_advanced_resize_horizontal: false,
		theme_advanced_statusbar_location: 'bottom',
		theme_advanced_blockformats: "p,h1,h2,h3,h4",
		relative_urls: false,
		remove_script_host: true,
		document_base_url: "<?php echo STANDARD_URL ?>/",
		skin: 'o2k7',
		skin_variant: 'silver',
		width: 610,
		height: 600,
		cleanup_on_startup: true,
		<?php echo $Biscuit->ExtensionTinyMce()->theme_css_setting($page) ?>
		external_link_list_url : "/tiny_mce_link_list",
		plugins : "table,style,iespell,insertdatetime,preview,media,searchreplace,contextmenu,directionality,fullscreen,noneditable,visualchars,nonbreaking,template,inlinepopups",
		<?php
		if ($PageContentManager->user_can_manage_pages()) {
			// Add the TinyBrowser plugin to the RTE:
			?>
		file_browser_callback : "tinyBrowser",
			<?php
		}
		?>
		setup: function(ed) {
			ed.onChange.add(function() {
				Biscuit.Session.KeepAlive.ping();
			});
		}
	});
</script>