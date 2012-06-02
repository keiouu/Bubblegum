<?php
/*
 * Tikapot Admin App Core
 *
 */

require_once(home_dir . "contrib/admin/formprinter.php");
require_once(home_dir . "contrib/admin/dataset.php");
require_once(home_dir . "contrib/admin/model.php");
require_once(home_dir . "contrib/admin/sidebar.php");
require_once(home_dir . "contrib/admin/index_panel.php");

abstract class AdminManager
{
	private static $models = array(), $sidebars = array(), $panels = array(), $menu_extra = array();
	
	public static function add($app, $model_admin) {
		if (!isset(AdminManager::$models[$app]))
			AdminManager::$models[$app] = array();
		AdminManager::$models[$app][] = $model_admin;
	}
	
	public static function is_registered($app, $class) {
		$class = strtolower($class);
		$app_array = AdminManager::get($app);		
		foreach ($app_array as $model_admin) {
			$t_class = strtolower(get_class($model_admin->get_model()));
			if ($t_class == $class) 
				return true;
		}
		return false;
	}
	
	public static function is_class_registered($class) {
		foreach (AdminManager::$models as $app => $ar) {
			if (AdminManager::is_registered($app, $class))
				return true;
		}
		return false;
	}
	
	public static function get_app_of_class($class) {
		foreach (AdminManager::$models as $app => $ar) {
			if (AdminManager::is_registered($app, $class))
				return $app;
		}
		return false;
	}
	
	public static function get_edit_link($obj) {
		$class = get_class($obj);
		foreach (AdminManager::$models as $app => $ar) {
			if (AdminManager::is_registered($app, $class)) {
				return admin_url . $app . '/' . $obj->_display_name() . '/edit/' . $obj->pk . '/';
			}
		}
		return '';
	}
	
	public static function get_add_link($obj) {
		$class = get_class($obj);
		foreach (AdminManager::$models as $app => $ar) {
			if (AdminManager::is_registered($app, $class)) {
				return admin_url . $app . '/' . $obj->_display_name() . '/add/';
			}
		}
		return '';
	}
	
	public static function get_all() {
		return AdminManager::$models;
	}
	
	public static function get($app, $class = null) {
		if ($class === null)
			return isset(AdminManager::$models[$app]) ? AdminManager::$models[$app] : array();
		// We want a specific app
		$class = strtolower($class);
		$app_array = AdminManager::get($app);	
		foreach ($app_array as $model_admin) {
			$t_class = strtolower(get_class($model_admin->get_model()));
			if ($t_class == $class) {
				return $model_admin;
			}
		}
		return null;
	}
	
	public static function register_sidebar($object) {
		AdminManager::$sidebars[] = $object;
	}
	
	public static function get_sidebars() {
		return AdminManager::$sidebars;
	}
	
	public static function register_panel($object) {
		AdminManager::$panels[] = $object;
	}
	
	public static function get_panels() {
		return AdminManager::$panels;
	}
	
	public static function register_menu_item($name, $link, $parent = null) {
		$item = array("link" => $link, "children" => array());
		if ($parent == null) {
			AdminManager::$menu_extra[$name] = $item;
		} else {
			if (isset(AdminManager::$menu_extra[$parent])) {
				AdminManager::$menu_extra[$parent]["children"][$name] = $item;
			} else {
				console_error($GLOBALS['i18n']['admin']['menu_err1'] . $parent);
			}
		}
	}
	
	public static function get_extra_menu() {
		return AdminManager::$menu_extra;
	}
}
?>

