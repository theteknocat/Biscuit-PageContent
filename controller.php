<?php
/**
 * Module for providing page content editing functionality via rich text editor. Requires Tiny MCE extension.
 * 
 * @package Modules
 * @author Peter Epp
 * @copyright Copyright (c) 2009 Peter Epp (http://teknocat.org)
 * @license GNU Lesser General Public License (http://www.gnu.org/licenses/lgpl.html)
 * @version 2.0
 **/
class PageContentManager extends AbstractModuleController {
	protected $_models = array(
		'Page' => 'Page'
	);
	/**
	 * List of other plugins this one is dependent on
	 *
	 * @var array
	 */
	protected $_dependencies = array("Authenticator","HtmlPurify","new" => "TinyMce", "edit" => "TinyMce", "manage_pages" => "TinyMce");
	/**
	 * List of actions that require an ID in the request, in addition to the base actions that always require an id (show, edit, delete)
	 *
	 * @var string
	 */
	protected $_actions_requiring_id = array('edit', 'delete');
	/**
	 * Place to cache the current page being edited for the special permission check
	 *
	 * @var Page
	 */
	private $_page_being_edited;
	/**
	 * Index action - by default find all items in the database and render
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_index() {
		if ($this->Biscuit->Page->slug() == 'content_editor') {
			Response::redirect($this->url('manage_pages'));
			return;
		}
		$this->set_view_var("page", $this->Biscuit->Page);
		$this->render();
	}
	/**
	 * Render a page listing all editable pages with administrative functionality
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_manage_pages() {
		$this->register_js("footer","content_editor.js");
		$this->register_css(array('filename' => 'page_manager.css', 'media' => 'screen'));
		$this->register_css(array('filename' => 'ie.css', 'media' => 'screen'),true);
		$pages = $this->Page->find_all_editable();
		$sorted_pages = $this->Biscuit->ExtensionNavigation()->sort_pages($pages);
		$page_list = $this->Biscuit->ExtensionNavigation()->render_pages_hierarchically($sorted_pages, 0, Navigation::WITH_CHILDREN, 'modules/page_content/views/manage_pages_list.php',array('top_level' => true, 'top_level_parent_id' => 0));
		if (!empty($page_list)) {
			$page_list = '<fieldset class="page-list-container" id="page-list-container-0"><legend>Main Menu</legend>'.$page_list.'</fieldset>';
		}
		$other_menus = $this->Biscuit->ExtensionNavigation()->other_menus();
		if (!empty($other_menus)) {
			foreach ($other_menus as $menu) {
				$menu_list = $this->Biscuit->ExtensionNavigation()->render_pages_hierarchically($sorted_pages, $menu->id(), Navigation::WITH_CHILDREN, 'modules/page_content/views/manage_pages_list.php',array('top_level' => true, 'top_level_parent_id' => $menu->id()));
				if (!empty($menu_list)) {
					$page_list .= '<fieldset class="page-list-container" id="page-list-container-'.$menu->id().'"><legend>'.$menu->name().'</legend>'.$menu_list.'</fieldset>';
				}
			}
		}
		$orphan_list = $this->Biscuit->ExtensionNavigation()->render_pages_hierarchically($sorted_pages, NORMAL_ORPHAN_PAGE, Navigation::WITH_CHILDREN, 'modules/page_content/views/manage_pages_list.php',array('top_level' => true, 'top_level_parent_id' => NORMAL_ORPHAN_PAGE));
		if (!empty($orphan_list)) {
			$page_list = $page_list.'<fieldset class="page-list-container" id="page-list-container-'.NORMAL_ORPHAN_PAGE.'"><legend>Orphan Pages</legend>'.$orphan_list.'</fieldset>';
		}
		$this->set_view_var('page_list',$page_list);
		$this->title('Manage Pages');
		$this->render();
	}
	/**
	 * Load a page model into the view vars and then defer to the parent action_edit method
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_edit($mode = 'edit') {
		$this->register_js("footer","content_editor.js");
		parent::action_edit($mode);
		$this->Biscuit->ExtensionTinyMce()->register_components();
		$this->register_css(array('filename' => 'page_manager.css', 'media' => 'screen'));
		$this->register_css(array('filename' => 'ie.css', 'media' => 'screen'),true);
		if ($this->user_can_manage_pages()) {
			$users = $this->Biscuit->ModuleAuthenticator()->User->find_all(array('first_name' => 'ASC','last_name' => 'ASC'));
			$this->set_view_var('user_select_list',Form::models_to_select_data_set($users,'id','full_name'));
			$access_levels = $this->Biscuit->ModuleAuthenticator()->access_levels();
			foreach ($access_levels as $access_level) {
				if ($access_level->id() <= $this->Biscuit->ModuleAuthenticator()->active_user()->user_level()) {
					$access_levels_for_view[] = $access_level;
				}
			}
			$this->set_view_var('access_select_list',Form::models_to_select_data_set($access_levels_for_view,'id','name'));
		}
	}
	/**
	 * Whether or not a user can delete a page. Checks if the page is allowed to be deleted and if so checks if normal permission check passes or if not then if the
	 * current user owns the page.
	 *
	 * @return bool
	 * @author Peter Epp
	 */
	public function user_can_delete(Page $page) {
		if (!$page->is_new() && $page->allow_delete() == 1) {
			$children_allow_delete = true;
			if ($page->has_children()) {
				$child_pages = $this->Page->find_all_children($page->id());
				foreach ($child_pages as $child_page) {
					if (!$child_page->allow_delete()) {
						$children_allow_delete = false;
						break;
					}
				}
			}
			if ($children_allow_delete) {
				return (Permissions::user_can($this,'delete') || ($this->Biscuit->ModuleAuthenticator()->user_is_logged_in() && $page->owner_id() == $this->Biscuit->ModuleAuthenticator()->active_user()->id()));
			}
		}
		return false;
	}
	/**
	 * Delete the requested page as well as all of it's children
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_delete() {
		$page = $this->Page->find($this->params['id']);
		if (!$page) {
			throw new RecordNotFoundException();
		}
		if (parent::confirm_deletion($page,'Page')) {
			if ($page->has_children()) {
				$child_pages = $this->Page->find_all_children($this->params['id']);
				if (!empty($child_pages)) {
					foreach ($child_pages as $child_page) {
						$url = $this->url('index',$child_page->id());
						if (!$child_page->delete()) {
							Session::flash('user_error', "Failed to remove the child page with URL ".$page->url().". This page will now be orphaned and not show up in any menus. This is likely due to a bug, so please contact the developer for assistance.");
						} else {
							Event::fire("successful_delete",$child_page,$url);
						}
					}
				}
			}
			// Now call the parent delete method to delete the actual page
			parent::action_delete();
		}
	}
	/**
	 * Special check to see if the current user can edit the page based on if they are super admin or if they own the page in question.
	 *
	 * @param Page $page 
	 * @return void
	 * @author Peter Epp
	 */
	public function user_can_edit(Page $page) {
		return (Permissions::user_can($this,'edit') || ($this->Biscuit->ModuleAuthenticator()->user_is_logged_in() && $page->owner_id() == $this->Biscuit->ModuleAuthenticator()->active_user()->id()));
	}
	/**
	 * Do a special permission check for the edit action taking into account page content ownership, otherwise just defer to normal permission check
	 *
	 * @param string $action 
	 * @return bool
	 * @author Peter Epp
	 */
	function user_can($action) {
		if ($action == 'edit') {
			if (empty($this->_page_being_edited)) {
				if (!empty($this->params['id'])) {
					$this->_page_being_edited = $this->Page->find($this->params['id']);
				} else {
					Console::log("WARNING: No Page model cached and no page id provided in params for edit permission check. User may be denied permission to edit.");
				}
			}
			return (Permissions::user_can($this,'edit') || ($this->Biscuit->ModuleAuthenticator()->user_is_logged_in() && $this->_page_being_edited !== null && $this->Biscuit->ModuleAuthenticator()->active_user()->id() == $this->_page_being_edited->owner_id()));
		}
		return Permissions::user_can($this,$action);
	}
	/**
	 * Secondary role is to set the page content in a view var for rendering when the action is "index"
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_secondary() {
		if ($this->action() == "index" || $this->action() == 'login') {
			$this->set_view_var("page", $this->Biscuit->Page);
			$this->render('index');
		}
	}
	/**
	 * Render option tags for all publicly accessible content managed pages
	 *
	 * @param int $id 
	 * @return string
	 * @author Peter Epp
	 */
	public function render_parent_option_list($parent_id = null,$main_menu_name = 'Main Menu',$orphan_menu_name = 'Orphan Pages') {
		if ($this->action() == 'edit') {
			$current_page_id = $this->params['id'];
		} else {
			$current_page_id = null;
		}
		$curr_user = $this->Biscuit->ModuleAuthenticator()->active_user();
		$pages = $this->Page->find_all_accessible_by($curr_user->user_level());
		$sorted_pages = $this->Biscuit->ExtensionNavigation()->sort_pages($pages);
		$parent_options = '';
		$parent_options .= '<option value="0"'.(($parent_id == 0) ? ' selected="selected"' : '').'>'.$main_menu_name.'</option>';
		$parent_options .= $this->Biscuit->ExtensionNavigation()->render_pages_hierarchically($sorted_pages, 0, Navigation::WITH_CHILDREN, 'modules/page_content/views/page_select_list_options.php', array('current_page_id' => $current_page_id, 'selected_parent' => $parent_id));
		$other_menus = $this->Biscuit->ExtensionNavigation()->other_menus();
		if (!empty($other_menus)) {
			foreach ($other_menus as $menu) {
				$parent_options .= '<option value="'.$menu->id().'"'.(($parent_id == $menu->id()) ? ' selected="selected"' : '').'>'.$menu->name().'</option>';
				$parent_options .= $this->Biscuit->ExtensionNavigation()->render_pages_hierarchically($sorted_pages, $menu->id(), Navigation::WITH_CHILDREN, 'modules/page_content/views/page_select_list_options.php', array('current_page_id' => $current_page_id, 'selected_parent' => $parent_id));
			}
		}
		$parent_options .= '<option value="'.NORMAL_ORPHAN_PAGE.'"'.(($parent_id == NORMAL_ORPHAN_PAGE) ? ' selected="selected"' : '').'>'.$orphan_menu_name.'</option>';
		return $parent_options;
	}
	/**
	 * Enforce the presence of some data(notably ID) for certain actions. This function
	 * is called before the action by AbstractModuleController#run
	 *
	 * @return boolean
	 **/
	public function before_filter() {
		if (in_array($this->action(), array('edit','delete'))) {
			// require ID
			return (!empty($this->params['id']));
		}
		return true;
	}
	public function return_url($model_name = null) {
		if ($this->action() == 'delete' || $this->action() == 'new') {
			return $this->url('manage_pages');
		}
		return parent::return_url($model_name);
	}
	public function url($action=null, $id=null) {
		if (empty($action)) {
			$action = 'index';
		}
		if ($action == 'show') {
			$action = 'index';
		}
		if ($action != 'manage_pages' && $action != 'new') {
			if ($id) {
				$page = $this->Page->find($id);
			} else if (!empty($this->params['id']) && ($action == 'index' || in_array($action,$this->_actions_requiring_id))) {
				$page = $this->Page->find($this->params['id']);
				$id = $page->id();
			}
			if (empty($page)) {
				Console::log('PageContent::url() cannot find page, or no ID provided');
				if ($this->user_can_manage_pages()) {
					return '/content_editor/manage_pages';
				}
				return '/';
			} else {
				$page_slug = $page->slug();
			}
			if (empty($action) || $action == "index") {
				return '/'.$page_slug;
			}
		}
		$url = '/content_editor/'.$action;
		if ($id) {
			$url .= '/'.$id;
		}
		return $url;
	}
	/**
	 * When page content is compiled, replace all canonical page URLs with pretty ones
	 *
	 * @param string $compiled_content 
	 * @return void
	 * @author Peter Epp
	 */
	protected function act_on_content_compiled() {
		if ($this->base_action_name($this->action()) != 'edit') {
			$compiled_content = $this->Biscuit->get_compiled_content();
			$all_pages = $this->Page->find_all();
			foreach ($all_pages as $page) {
				$compiled_content = preg_replace('/\/canonical-page-link\/'.$page->id().'\/\"/',$page->url().'"',$compiled_content);
				$compiled_content = preg_replace('/(\/canonical-page-link\/'.$page->id().')\/([^\"]+)?\"/',$page->url().'/$2"',$compiled_content);
			}
			$this->Biscuit->set_compiled_content($compiled_content);
		}
	}
	protected function act_on_compile_footer() {
		if ($this->is_primary() && ($this->action() == 'edit' || $this->action() == 'new')) {
			$tb_browser_script = $this->Biscuit->ExtensionTinyMce()->render_tinymce_tb_browser_script();
		} else if ($this->is_primary() && $this->action() == 'manage_pages') {
			$tb_browser_script = $this->Biscuit->ExtensionTinyMce()->render_standalone_tb_browser_script();
		}
		if (!empty($tb_browser_script)) {
			$this->Biscuit->append_view_var('footer',$tb_browser_script);
		}
	}
	/**
	 * Run migrations required for module to be installed properly
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public static function install_migration() {
		$content_editor_page = DB::fetch_one("SELECT `id` FROM `page_index` WHERE `slug` = 'content_editor'");
		if (!$content_editor_page) {
			// Add content_editor page (public access by default):
			DB::insert("INSERT INTO `page_index` SET `parent` = 9999999, `slug` = 'content_editor', `title` = 'Content Editor'");
			// Get module row ID:
			$module_id = DB::fetch_one("SELECT `id` FROM `modules` WHERE `name` = 'PageContent'");
			// Remove PageContent from module pages first to ensure clean install:
			DB::query("DELETE FROM `module_pages` WHERE `module_id` = {$module_id} AND `page_name` = 'content_editor'");
			// Add PageContent to content_editor page:
			DB::insert("INSERT INTO `module_pages` SET `module_id` = {$module_id}, `page_name` = 'content_editor', `is_primary` = 1");
		}
		if (!DB::column_exists_in_table('content','page_index')) {
			DB::query("ALTER TABLE `page_index` ADD COLUMN `content` longtext, ADD COLUMN `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
		}
		if (!DB::column_exists_in_table('allow_delete','page_index')) {
			DB::query("ALTER TABLE `page_index` ADD COLUMN `allow_delete` tinyint NOT NULL DEFAULT '1'");
			DB::query("UPDATE `page_index` SET `allow_delete` = 0 WHERE `parent` = 9999999 OR `slug` = 'index' OR `slug` = 'login' OR `slug` = 'users'");
		}
		if (!DB::column_exists_in_table('owner_id','page_index')) {
			DB::query("ALTER TABLE `page_index` ADD COLUMN `owner_id` int(8) NOT NULL DEFAULT 1, ADD KEY `owner_id` (`owner_id`), ADD CONSTRAINT `page_index_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE");
		}
		if (!DB::column_exists_in_table('exclude_from_nav','page_index')) {
			DB::query("ALTER TABLE `page_index` ADD COLUMN `exclude_from_nav` int(1) NOT NULL DEFAULT 0");
		}
		Permissions::add(__CLASS__,array('new' => 99, 'edit' => 99, 'delete' => 99, 'manage_pages' => 99),true);
	}
	/**
	 * Run migrations to properly uninstall the module
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public static function uninstall_migration() {
		$module_id = DB::fetch_one("SELECT `id` FROM `modules` WHERE `name` = 'PageContent'");
		DB::query("DELETE FROM `page_index` WHERE `slug` = 'content_editor'");
		DB::query("DELETE FROM `module_pages` WHERE `module_id` = ".$module_id);
		DB::query("ALTER TABLE `page_index` DROP COLUMN `content`, DROP COLUMN `updated`, DROP COLUMN `allow_delete`, DROP FOREIGN KEY `page_index_ibfk_1`, DROP COLUMN `owner_id`");
		Permissions::remove(__CLASS__);
	}
	/**
	 * Provide special rewrite rule for the manage_pages action
	 *
	 * @return array
	 * @author Peter Epp
	 */
	public static function rewrite_rules() {
		return array(
			array(
				'pattern' => '/^content_editor\/manage_pages$/',
				'replacement' => 'page_slug=content_editor&action=manage_pages'
			)
		);
	}
	/**
	 * Custom method for adding extra breadcrumbs that only adds crumb for the current action if it's not "manage_pages"
	 *
	 * @param Navigation $Navigation 
	 * @return void
	 * @author Peter Epp
	 */
	protected function act_on_build_breadcrumbs($Navigation) {
		if ($this->action() != 'manage_pages') {
			parent::act_on_build_breadcrumbs($Navigation);
		}
	}
}
?>