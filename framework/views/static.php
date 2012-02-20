<?php
/*
 * Tikapot Static View
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "framework/tpcache.php");

class StaticView extends View
{
	protected $version, $expiry;

	public function __construct($url, $page = "", $version = "1", $expiry = 86400) {
		$this->version = $version;
		$this->expiry = $expiry;
		parent::__construct($url, $page);
	}
	
	public function render($request, $args) {
		// First, check to ensure memcache works
		if (!TPCache::avaliable()) {
			return parent::render($request, $args);
		}
		
		// It does! Check the cache out..
		$cache = TPCache::get($this->version.$this->page);
		if ($cache !== false) {
			return $cache;
		}
		
		// Doesnt exist! Render and add
		ob_start();
		print parent::render($request, $args);
		$page = ob_get_clean();
		TPCache::set($this->version.$this->page, $page, $this->expiry);
		
		$cache = TPCache::getCache();		
		return $page;
	}
}

?>
