<?php
/*
 * Tikapot Utilities
 *
 */
 
function starts_with($haystack, $needle) {
	if (function_exists('tp_str_begins'))
		return tp_str_begins($haystack, $needle);

	return substr($haystack, 0, strlen($needle)) === $needle;
}
 
function partition($haystack, $needle) {
	if (function_exists('tp_str_partition'))
		return tp_str_partition($haystack, $needle);

	$pos = strpos($haystack, $needle);
	if ($pos > 0)
		return array(substr($haystack, 0, $pos), $needle, substr($haystack, $pos + strlen($needle), strlen($haystack)));
	return array($haystack, $needle, "");
}
 
function ends_with($haystack, $needle) {
	if (function_exists('tp_str_ends'))
		return tp_str_ends($haystack, $needle);

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
?>
 
