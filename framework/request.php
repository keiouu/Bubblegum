<?php
/*
 * Tikapot Request Class
 *
 */

require_once(home_dir . "framework/i18n.php");

class Request
{
	public $method, $page, $get, $post, $cookies, $mimeType, $messages, $safe_vals;
	
	public function __construct() {
		$this->method = "GET";
		if (count($_POST) > 0)
			$this->method = "POST";
		$this->get = $_GET;
		$this->post = $_POST;
		$this->vars = $_REQUEST;
		$this->cookies = $_COOKIE;
		$this->page = "/";
		if (isset($this->get[page_def])) {
			$this->page = trim($this->get[page_def]);
		}
		if (isset($this->page[0]) && $this->page[0] == '/') {
			$this->page = substr($this->page, 1);
		}
		$this->fullPath = home_url . $this->page;
		$this->page = '/' . $this->page;
		$this->mimeType = $this->get_mime_type($this->page);
		if (isset($this->get[page_def]))
			unset($this->get[page_def]);
		$this->visitor_ip = $this->getIP();
		$this->messages = isset($_SESSION['request_messages']) ? $_SESSION['request_messages'] : array();
		$this->safe_vals = array();
		$this->add_val("home_url", home_url);
		$this->add_val("media_url", media_url);
		$this->add_val("project_name", project_name);
		$this->add_val("page_url", $this->fullPath);
		$this->add_val("csrf_token", $this->get_csrf_token());
		$this->init_i18n();
	}
	
	public function get_csrf_token() {
		$token = md5(uniqid(rand(), true));
		if (!isset($_SESSION["tprequesttokens"]))
			$_SESSION["tprequesttokens"] = array();
		$_SESSION["tprequesttokens"][] = $token;
		return $token;
	}
	
	public function validate_csrf_token($token) {
		if (in_array($token, $_SESSION["tprequesttokens"])) {
			$backup = $_SESSION["tprequesttokens"];
			$_SESSION["tprequesttokens"] = array();
			foreach ($backup as $val)
				if ($val !== $token)
					$_SESSION["tprequesttokens"][] = $val;
			return true;
		}
		return false;
	}
	
	public function query_string() {
		$query = "";
		foreach ($this->get as $key => $val) {
			if ($query !== "")
				$query .= "&";
			$query .= $key . "=" . $val;
		}
		return $query;
	}
	
	private function get_query_array($query) {
		if (strlen($query) == 0)
			return array();
		$vars = explode("&", $query);
		$array = array();
		foreach ($vars as $var) {
			list($key, $value) = explode("=", $var, 2);
			$array[$key] = $value;
		}
		return $array;
	}
	
	private function merge_query_strings($querya, $queryb) {
		$array = array_merge($this->get_query_array($querya), $this->get_query_array($queryb));
		$string = "";
		foreach ($array as $key => $value) {
			if ($string !== "")
				$string .= "&";
			$string .= $key . "=" . $value;
		}
		return $string;
	}
	
	public function create_url($url, $start_query, $query_additions = "") {
		$query = $this->merge_query_strings($start_query, $query_additions);
		return $url . "?" . $query;
	}
	
	private function init_i18n() {	
		if (isset($this->get['langswitch'])) {
			$_SESSION['lang'] = $this->get['langswitch'];
			$file = isset($_SESSION['lang']) ? $_SESSION['lang'] : "en";
			$filename = home_dir . "i18n/" . $file . ".php";
			if (!strpos($file, "..") && file_exists($filename))
				require($filename);
			else
				require(home_dir . "i18n/en.php");
			$GLOBALS["i18n"] = $i18n_data;
		}
		
		$this->i18n = new i18n($GLOBALS["i18n"]);
	}
	
	public function add_val($name, $val) {
		$this->safe_vals[$name] = $val;
	}
	
	/*
	 * Messaging framework for requests
	 */
	public function message($message, $type = "info") {
		if (!isset($this->messages[$type]))
			$this->messages[$type] = array();
		$this->messages[$type][] = $message;
		$_SESSION['request_messages'] = $this->messages;
	}
	
	public function delete_messages() {
		$this->messages = array();
		$_SESSION['request_messages'] = array();
	}
	
	public function get_messages() {
		return $this->messages;
	}
	
	public function print_messages() {
		foreach ($this->messages as $type => $messages) {
			print '<div class="messages '.$type.'">';
			foreach ($messages as $message) {
				print '<div class="message"><p>' . $message . '</p></div>';
			}
			print '</div>';
		}
	}
	
	public function print_and_delete_messages() {
		$this->print_messages();
		$this->delete_messages();
	}
	
	public function getFullPath() {
		return $this->fullPath;
	}
	
	public function getIP() {
		if (strlen(getenv("HTTP_CLIENT_IP")) > 0)
			return getenv("HTTP_CLIENT_IP");
		if (strlen(getenv("HTTP_X_FORWARDED_FOR")) > 0)
			return getenv("HTTP_X_FORWARDED_FOR");
		if (strlen(getenv("REMOTE_ADDR")) > 0)
			return getenv("REMOTE_ADDR");
		return "UNKNOWN";
	}
	
	function get_mime_type($filename) {
		// Tikapot 1.1 change:
		//    We now assume that a web server filters out media files etc.
		//    So anything we are displaying should be a html file.
		//    However, in the unlikely event there is a "." we assume a view
		//    has been created that serves a file type. Lets support some of
		//    the common ones. The others can send their own headers..
		if (strpos($filename, ".") === false)
			return "text/html";
		
		// Something is sending a file.. lets try and find out what
		$fileext = substr(strrchr($filename, '.'), 1);
		switch ($fileext) {
			case "css":
				return "text/css";
			case "js":
				return "text/javascript";
			case "php":
			case "html":
				return "text/html";
			case "txt":
				return "text/plain";
			default:
				// Nope! Its unknown
				return "unknown";
		}
	}
}

?>

