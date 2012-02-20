<?php
/*
 * Tikapot Cookie Manager
 *
 */

require_once(home_dir . "framework/config_manager.php");

class CookieManager
{
	public static function set($key, $value, $expires = 3600, $path = '/') {
		if (!ConfigManager::get("disable_cookies"))
			setcookie($key, $value, time() + $expires, $path);
	}
	
	public static function get($key) {
		if (!ConfigManager::get("disable_cookies"))
			return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
		return null;
	}
	
	public static function delete($key, $path = '/') {
		if (!ConfigManager::get("disable_cookies"))
			setcookie($key, "", time() - 3600, $path);
	}
}

?>
