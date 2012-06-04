<?php
/*
 * Tikapot Media File Handler
 *
 */

require_once(home_dir . "framework/utils.php");
require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "framework/processing/processors/mediamanager_post_processor.php");
require_once(home_dir . "lib/jsmin.php");


class MediaManager
{
	private static $_processor_registered = false;
	private $media_files = array(), $media_key = "", $media_dir = "", $media_url = "";
	
	public function __construct($media_key = "style", $dir_override = "", $url_override = "") {
		$this->media_key = $media_key;
		$this->media_dir = strlen($dir_override) > 0 ? $dir_override : media_dir;
		$this->media_url = strlen($url_override) > 0 ? $url_override : media_url;
	}
	
	/**
	 * Enable the MediaManager Post Processor
	 */
	public function enable_processor() {
		if (!MediaManager::$_processor_registered) {
			new Media_Manager_Post_Processor();
			MediaManager::$_processor_registered = true;
		}
	}
	
	public function get_media_dir() {
		return $this->media_dir;
	}
	
	public function get_media_url() {
		return $this->media_url;
	}
	
	public function add_file($file) {
		$this->media_files[] = $file;
	}
	
	public function count_files() {
		return count($this->media_files);
	}
	
	public function build($ext) {
		if (!file_exists($this->get_media_dir() . "cache/") || !is_dir($this->get_media_dir() . "cache/")) {
			if (!mkdir($this->get_media_dir() . "cache/", "0744")) {
				console_error($GLOBALS['i18n']['framework']['media_error']);
				return;
			}
		}
		
		$filename = $this->get_media_dir() . "cache/" . $this->media_key . "." . $ext;
		$fileurl  = $this->get_media_url() . "cache/" . $this->media_key . "." . $ext;
		if (file_exists($filename) && !ConfigManager::get("dev_mode", false)) { // Disable caching in dev mode
			return $fileurl;
		}
		
		// Gather all data
		$data = "";
		foreach($this->media_files as $key => $file) {
			if (ends_with($file, $ext)) {
				$data .= "\n" . file_get_contents($file);
			}
		}
		
		// Minify
		switch ($ext) {
			case "css":
				$data = preg_replace('/\n\s*\n/',"\n", $data);
				$data = preg_replace('!/\*.*?\*/!s','', $data);
				$data = preg_replace('/[\n\t]/',' ', $data);
				$data = preg_replace('/ +/',' ', $data);
				$data = preg_replace('/ ?([,:;{}]) ?/','$1',$data);
				break;
			case "js":
				$data = JSMin::minify($data);
				break;
		}

		// Write
		if (file_put_contents($filename, trim($data)) === FALSE) {
			console_error($GLOBALS['i18n']['framework']['media_error_write']);
		}
		
		return $fileurl;
	}
	
	public function build_css() {
		return $this->build("css");
	}
	
	public function build_js() {
		return $this->build("js");
	}
}

?>
