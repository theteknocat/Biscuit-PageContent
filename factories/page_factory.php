<?php
/**
 * Custom factory for the PageContent model, since it's involves some special functionality when finding a page by id
 *
 * @package Modules
 * @subpackage PageContent
 * @author Peter Epp
 * @version $Id: page_factory.php 13843 2011-07-27 19:45:49Z teknocat $
 */
class PageFactory extends ModelFactory {
	/**
	 * Override default find method so we can set default content and last updated date if they have not yet been set
	 *
	 * @param string $id 
	 * @return Page
	 * @author Peter Epp
	 */
	public function find($id) {
		$page = parent::find($id);
		if ($page) {
			$set_default_content = false;
			$set_default_date = false;
			if ($page->parent() != 9999999) {
				if ($page->updated() == '0000-00-00 00:00:00') {
					$date = new Date();
					$page->set_updated($date->format('Y-m-d H:i:s'));
					$set_default_date = true;
					$page->save();
				}
			} 
		}
		return $page;
	}
	/**
	 * Find all regular, public pages (ie. pages with public access level that's not marked as external to all menus by parent id 9999999)
	 *
	 * @return array
	 * @author Peter Epp
	 */
	public function find_all_accessible_by($user_access_level = PUBLIC_USER) {
		$query = "SELECT * FROM `page_index` WHERE `parent` != 9999999 AND `access_level` <= {$user_access_level} ORDER BY `slug`, `sort_order`";
		return parent::models_from_query($query);
	}
	/**
	 * Find all regular, public pages that have the PageContent module installed as primary and are thereby editable. Pages are retrieved un-sorted. The PageContent
	 * Controller provides functions needed for sorting the page properly for output.
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public function find_all_editable() {
		// Get the ID of this module:
		$module_id = DB::fetch_one("SELECT `id` FROM `modules` WHERE `name` = 'PageContent'");
		$active_user_level = Biscuit::instance()->ModuleAuthenticator()->active_user()->user_level();
		$query = "SELECT `pi`.* FROM `page_index` `pi` LEFT JOIN `module_pages` `mp` ON (`pi`.`slug` = `mp`.`page_name` AND `mp`.`module_id` = {$module_id}) WHERE `pi`.`parent` != 9999999 AND `pi`.`access_level` <= {$active_user_level} AND `mp`.`id` IS NOT NULL";
		return parent::models_from_query($query);
	}
	/**
	 * Recursively find all children of a page with a given ID
	 *
	 * @param int $id 
	 * @return array|bool Array of page object instances or false if there are none
	 * @author Peter Epp
	 */
	public function find_all_children($id) {
		$children = DB::fetch("SELECT * FROM `page_index` WHERE `parent` = ?",$id);
		$child_pages = array();
		if (!empty($children)) {
			foreach ($children as $child_data) {
				$child_page = parent::create($child_data);
				$child_pages[] = $child_page;
				if ($child_page->has_children()) {
					$childs_children = $this->find_all_children($child_page->id());
					if (!empty($childs_children)) {
						foreach ($childs_children as $childs_child) {
							$child_pages[] = $childs_child;
						}
					}
				}
			}
			return $child_pages;
		}
		return null;
	}
}
?>