<?php
/*
 * Tikapot Memcached extension
 *
 */

class CacheException extends Exception { }

require_once(home_dir . "framework/config_manager.php");

abstract class TPCache
{
	static private $cache = null;
	
	public static function avaliable() {
		return ConfigManager::get('enable_cache', false) && class_exists("Memcached");
	}
	
	public static function getCache() {
		if (!ConfigManager::get('enable_cache', false))
			return null;
		
		// If it isnt disabled, the user must have enabled it and believe it to be avaliable,
		// if it isnt.. let them know!
		if (!class_exists("Memcached"))
			throw new CacheException($GLOBALS["i18n"]["cacheerr1"]);
			
		if (isset(TPCache::$cache) && TPCache::$cache !== null)
			return TPCache::$cache;
		
		global $caches;
		$cache = new Memcached(project_name);
		foreach ($caches as $server => $arr) {
			$cache->addServer($arr["host"], $arr["port"]);
		}
		$cache->setOption(Memcached::OPT_PREFIX_KEY, ConfigManager::get('cache_prefix', "tp_"));
		TPCache::$cache = $cache;
		return TPCache::$cache;
	}
	
	public static function get($key) {
		$cache = TPCache::getCache();
		return $cache->get($key);
	}
	
	public static function set($key, $value) {
		$cache = TPCache::getCache();
		return $cache->set($key, $value);
	}
}

?>
