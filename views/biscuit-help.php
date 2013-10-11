<div id="help-tabs">
	<ul>
		<li><a href="#help-common-functionality">Basics</a></li>
		<li><a href="#help-page-management">Page Management</a></li>
		<li><a href="#help-page-edit-form">Page Edit Form</a></li>
	</ul>
	<div id="help-common-functionality">
		<p>The page content management module provides the functionality to add, edit, delete and re-order pages in your website, as well as edit arbitrary static formatted content for any of the pages on which the module is installed. Any page that uses this module will have an administration bar at the top of the page that looks like this:</p>
		<p><img src="/modules/page_content/images/help/admin-bar.jpg" alt="Administratino bar"></p>
		<p>The buttons it provides may vary depending on your access level. If you only have permission to edit certain pages, then you will only see the "Edit Content" button, for example.</p>
		<h4>Button Functions</h4>
		<ul>
			<li><strong>Manage Pages</strong><br>
				If you have this button, click on it to go to the page management interface. Click the "Page Management" help tab above for more information.</li>
			<li><strong>New Sub-Page</strong><br>
				If you have this button, click on it to create a new sub-page under the current page. The new page will then appear in the menu as a sub-page of the current page.</li>
			<li><strong>Edit Content</strong><br>
				If you have this button, click on it to edit the static content of the current page. Note that if the page also contains other types of content, for example news articles, this button will not allow you to edit those items. Other buttons will be provided for modifying other types of content on the same page.</li>
			<li><strong>Delete this Page</strong><br>
				If you have this button, click on it to delete the current page. You will be prompted for confirmation. Please not that if you see this button on some pages but not others, that's because some pages are explicitly set to prevent deletion by your web developer, generally to prevent problems with deleting pages that have other types of content in addition to the static content. If you need to delete one of those pages, you'll need to contact your developer. In addition, the home page cannot be deleted since your site must always have a home pgae.</li>
		</ul>
	</div>
	<div id="help-page-management">
		<p>The page management interface gives you an overview of all the pages of your site, indicating hierarchy and offering functions to re-order, edit and delete where applicable. For the most part it's very straightforward.</p>
		<p>At the top you'll see an administration bar with common options:</p>
		<p><img src="/modules/page_content/images/help/page-management-admin-bar.jpg" alt="Admin bar"></p>
		<p><strong>Manage Files</strong> will open the file manager in a new window. This lets you manage all the files and/or images uploaded to your site. This button is just provided for convenience, in case all you want to do is organize files or media without having to edit a page to access it.</p>
		<p><strong>Note:</strong> You may see separate "Manage Files" and "Manage Images" buttons. If that is the case, it just means you are using a different file manager than the stock Biscuit one, which is normal for older sites.</p>
		<h4>Page List(s)</h4>
		<p>Most sites will have one list of pages under "Main Menu". Your site may have other menus or sections of pages, in which case you may see more than one. The pages within a list look like this:<br>
			<img src="/modules/page_content/images/help/page-list.jpg" alt="Page list"></p>
		<p>As you can see there are various buttons for performing functions on pages:</p>
		<ul>
			<li><strong>Edit</strong><br>
				Click to edit the static page content.</li>
			<li><strong>Delete</strong><br>
				Click to delete the page. You will be prompted for confirmation. Note that some pages cannot be deleted, such as the home page or pages that have been setup with other special content types by your web developer.</li>
			<li><strong>New Sub-Page</strong><br>
				Click to create a new sub-page under the current page.</li>
			<li><strong>Drag Widget (<img src="/modules/page_content/images/help/drag-widget.jpg" alt="Drag widget" style="vertical-align: bottom">)</strong><br>
				Click and drag this widget to sort the page within it's current section. Note that when there is only one page in a section, it will have no drag widget as it cannot be sorted.</li>
		</ul>
	</div>
	<div id="help-page-edit-form">
		<h4>Page Form Fields</h4>
		<ul>
			<li><strong>Title</strong> (required)<br>
				This is the title of the page as you want it to appear at the top of the page. It will also be used for menu links, breadcrumbs and the browser title bar, unless you enter the optional Navigation Label (see below).</li>
			<li><strong>Navigation Label</strong> (optional)<br>
				Fill in this field if you want the menu link, breadcrumbs, and browser title to be different than the page title. This is useful in cases where you want a long title in the page, but a short and sweet label for menus, breadcrumbs etc.</li>
			<li><strong>Redirect URL</strong> (optional)<br>
				Use this field ONLY if you want the page to simply redirect somewhere else instead of providing content. This is useful if you want a link in your menu that goes to an external website, or is a shortcut to another section of your website. Enter either a full qualified URL (eg. http://example.co), or a relative URL for your site (eg. /test-page).</li>
			<li><strong>Content</strong> (optional)<br>
				This field is optional for cases where the main content of the page is populated by other modules and you do not need any static information as well. The field has a rich text editor with a toolbar similar to a word processor. Most of it's layout and formatting features are self explanatory. Please see the <a href="/user-help/TinyMce">Tiny MCE Help page</a> for more detailed help on using the rich text editor.</li>
			<li><strong>Description</strong> (optional)<br>
				Enter a description of the page content for search engines. Note that this field is not always useful and some search engines ignore it. Be careful what you enter here, as poor content can cause your site to lose ranking. Consult a search engine marketer for expert advise on how best to fill it out.</li>
			<li><strong>Keywords</strong> (optional)<br>
				Enter keywords relevant to the content on the page for search engines. This field can be highly useful in improving search engine rankings, but poor choice of keywords can yield bad results. Consult a search engine marketer for expert advise on how best to fill it out.</li>
			<li><strong>Parent Menu</strong> (required)<br>
				Choose the parent menu item in which this page belongs. Note that if you click a "New Sub-Page" button this will be pre-selected so the new page goes under the desired page. However you can of course change your mind. Note that this is the field you must change if you want to move an existing page into a different section.</li>
			<li><strong>Exclude From Nav</strong> (required)<br>
				Choose "Yes" if you do not want the page to appear in the menu.</li>
			<li><strong>Owner</strong> (required)<br>
				This field is only available if you have permission to assign page permissions. It allows you to give another user the power to edit the page. They will not be able to modify page permissions, only edit the content.</li>
			<li><strong>Access Level</strong> (required)<br>
				This field is only available if you have permission to assign page permissions. This sets the level of access required in order to see the page. Use this feature if you wish to make pages non-public, so a user must be logged in with the specified user level (or higher) in order to see it.</li>
		</ul>
	</div>
</div>