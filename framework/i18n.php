<?php
/*
 * Tikapot internationalisation
 *
 */

require_once(home_dir . "framework/config_manager.php");

class i18n implements Iterator, Countable, arrayaccess
{
	private $map;
	
	public function __construct($map) {
		if (is_array($map)) {
			$this->map = $map;
		}
	}
	
	public static function Init() {
		global $app_paths, $apps_list;
		Profiler::start("load_i18n");
		
		// Decide on file to load
		$file = isset($_SESSION['lang']) ? $_SESSION['lang'] : ConfigManager::get('default_i18n', "en");
		$file = str_replace(".", "", $file);
		@setlocale(LC_ALL, $file);
		
		// Deprecated - Load old i18n
		$filename = home_dir . "i18n/" . $file . ".php";
		if (file_exists($filename))
			require($filename);
		else
			require(home_dir . "i18n/en.php");
		$GLOBALS["i18n"] = $i18n_data;
		$i18n_data = array();
		
		// Load Framework i18n
		$filename = home_dir . "framework/i18n/" . $file . ".php";
		if (file_exists($filename))
			require($filename);
		else
			require(home_dir . "framework/i18n/en.php");
		$GLOBALS["i18n"]["framework"] = $i18n_data;
		$i18n_data = array();
		
		// Per-App i18n
		foreach ($apps_list as $app) {
			foreach ($app_paths as $app_path) {
				$dir = home_dir . $app_path . "/" . $app . "/i18n/";
				$filename = $dir . $file . ".php";
				if (!file_exists($filename))
					$filename = $dir . "en.php";
				if (file_exists($filename)) {
					include($filename);
					$GLOBALS["i18n"][$app] = $i18n_data;
					$i18n_data = array();
					break;
				}
			}
		}
		Profiler::end("load_i18n");
	}
	
	
	public function __get($name) {
		return $this->map[$name];
	}

	public function __isset($name) {
		return isset($this->map[$name]);
	}

	public function buildJS() {
		$js = "var i18n = new Object();\n";
		foreach ($this->map as $name => $val) {
			$name = str_replace(" ", "_", $name);
			if (!preg_match("/^[a-z]/", $name)) {
				$name = "i" . $name;
			}
			$val = str_replace("'", "\\'", $val);
			$val = str_replace("\n", "\\n", $val);
			$js .= "i18n." . $name . " = '".$val."';\n";
		}
		return $js;
	}
	
	public function count() {
		return count($map);
	}
	
	/* Iterator */
	public function rewind() {
		reset($this->map);
	}
	
	public function current() {
		return current($this->map);
	}
	
	public function key() {
		return key($this->map);
	}
	
	public function next() {
		return next($this->map);
	}
	
	public function valid() {
		$key = key($this->map);
		return $key !== NULL && $key !== FALSE;
	}
	/* End Iterator */
	
	/* Array Access */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->map[] = $value;
		} else {
			$this->map[$offset] = $value;
		}
	}
	
	public function offsetExists($offset) {
		return isset($this->map[$offset]);
	}
	
	public function offsetUnset($offset) {
		unset($this->map[$offset]);
	}
	
	public function offsetGet($offset) {
		if (isset($this->map[$offset]))
			return $this->map[$offset];
		return debug ? "#mtrns#" : "";
	}
	/* End Array Access */
}

?>

