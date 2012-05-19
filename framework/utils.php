<?php
/*
 * Tikapot Utilities
 *
 */

function starts_with($haystack, $needle) {
	return ($needle != "") && substr($haystack, 0, strlen($needle)) === $needle;
}

function partition($haystack, $needle) {
	$pos = strpos($haystack, $needle);
	if ($pos !== false)
		return array(substr($haystack, 0, $pos), $needle, substr($haystack, $pos + strlen($needle), strlen($haystack)));
	return array($haystack, $needle, "");
}

function ends_with($haystack, $needle) {
	return strrpos($haystack, $needle) === strlen($haystack)-strlen($needle);
}

function get_named_class($class, $app_name = null) {
	if (!class_exists($class)) {
		global $app_paths;
		foreach ($app_paths as $app_path) {
			$path = home_dir . $app_path . '/';
			if (file_exists($path) && ($handle = opendir($path))) {
				while (($entry = readdir($handle))  !== false) {
					if ($app_name !== null && $app_name !== $entry)
						continue;
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

function get_file_extension($filename, $delimiter = ".") {
	return substr(strrchr($filename, $delimiter), 1);
}

function get_file_name($filename, $delimiter = ".") {
	$filename = basename($filename, "." . get_file_extension($filename, $delimiter));
	$pos = strpos($filename, $delimiter);
	if ($pos === 0 && strrpos($filename, $delimiter) == $pos)
		return "";
	return $filename;
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
	// Add underscores before capitol letters. Turns "AnExample" into "An_Example"
	// An underscore is more reliable than a space
	$string = preg_replace('/([a-z])([A-Z])/', '$1_$2', $string);
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
	$new_string = "";
	foreach (explode(" ", $string) as $word) {
		$result = $new_string . ($new_string == "" ? "" : " ") . $word;
		if ($new_string == "" || strlen($result) <= $length - 3)
			$new_string = $result;
	}
	return substr($new_string, 0, $length - 3) . "...";
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
	if (PHP_SAPI === 'cli')
		print_r($var);
}

/**
 * Turns console logging on
 */
function console_on() {
	$GLOBALS['enable_console'] = true;
}

/**
 * Turns console logging off
 */
function console_off() {
	$GLOBALS['enable_console'] = false;
}

function console_log($val) {
	if (isset($GLOBALS['enable_console']) && !$GLOBALS['enable_console']) {
		return;
	}
	
	if (!isset($GLOBALS['console'])) {
		$GLOBALS['console'] = array();
	}
	
	$GLOBALS['console'][] = $val;
	if (PHP_SAPI === 'cli') {
		print $val;
	}
}

function console_print($val) {
	console_log($val);
}

function console_warn($val) {
	console_log('<span class="console_warning">'.$val.'</span>');
}

function console_error($val) {
	console_log('<span class="console_error">'.$val.'</span>');
}
?>
 
