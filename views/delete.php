<p><strong>Are you sure you want to delete <?php echo $representation ?>?</strong></p>
<?php
if ($representation->has_children()) {
	?>
<p class="notice"><strong>WARNING:</strong> Deleting this page will also remove all of it's child pages.</p>
	<?php
}
?>
<p>This action cannot be undone.</p>
<?php require('modules/generic_views/common_delete_confirmation_form.php') ?>