<?php
/*
 * Tikapot Utilities
 *
 */
 
function starts_with($haystack, $needle) {
	return substr($haystack, 0, strlen($needle)) === $needle;
}
 
function partition($haystack, $needle) {
	$pos = strpos($haystack, $needle);
	if ($pos > 0)
		return array(substr($haystack, 0, $pos), $needle, substr($haystack, $pos + strlen($needle), strlen($haystack)));
	return array($haystack, $needle, "");
}
 
function ends_with($haystack, $needle) {
	return strrpos($haystack, $needle) === strlen($haystack)-strlen($needle);
}

function get_named_class($class) {
	if (!class_exists($class)) {
		global $app_paths;
		foreach ($app_paths as $app_path) {
			$path = home_dir . $app_path . '/';
			if (file_exists($path) && ($handle = opendir($path))) {
				while (($entry = readdir($handle))  !== false) {
					if ($entry !== "." && $entry !== "..") {
						$file = $path . $entry . "/models.php";
						if (is_file($file)) {
							include_once($file);
							if (class_exists($class))
								break;
						}
					}
				}
				closedir($handle);
			}
		}
	}
	if (class_exists($class))
		return new $class();
	return null;
}

function get_file_extension($filename) {
	return substr(strrchr($filename, '.'), 1);
}

function email_sanitize($str) {
	$injections = array(
		'/(\n+)/i',
		'/(\r+)/i',
		'/(\t+)/i',
		'/(%0A+)/i',
		'/(%0D+)/i',
		'/(%08+)/i',
		'/(%09+)/i'
	);
	return preg_replace($injections, '', $str);
}

function prettify($string) {
	// Add underscores before capitol words. Turns "AnExample" into "An Example"
	// An underscore is more reliable than a space
	$string = preg_replace('/\B([A-Z])([a-z])/', '_$1$2', $string);
	// Turn underscores from above, and before that, into spaces
	$string = str_replace("_", " ", $string);
	// Upperwords!
	$string = ucwords($string);
	return $string;
}

function urlCheck($url) {
	if (function_exists('idn_to_ascii'))
		$url = idn_to_ascii($url);
	return filter_var($url, FILTER_VALIDATE_URL);
}

function ellipsize($string, $length) {
	if (strlen($string) <= $length)
		return $string;
	return substr($string, 0, $length - 3) . "..."; // TODO - break at words
}

function rmrf($dir) {
	if (!is_dir($dir))
		return;
	$objects = scandir($dir);
	foreach ($objects as $object) {
		if ($object == "." || $object == "..")
			continue;
		
		if (is_dir($dir . "/" . $object))
			rmrf($dir . "/" . $object);
		else
			unlink($dir . "/" . $object);
	}
	rmdir($dir);
}

/* Debugging utilities */
function analyze($var) {
	ob_start();
	print_r($var);
	console_log(ob_get_clean());
}

function console_log($val) {
	if (!isset($GLOBALS['console']))
		$GLOBALS['console'] = array();
	$GLOBALS['console'][] = $val;
}

function console_warn($val) {
	console_log($val); // TODO
}


