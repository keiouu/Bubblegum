<?php
/*
 * Tikapot internationalisation
 *
 */

class i18n implements Iterator, Countable, arrayaccess
{
	private $map;
	
	public function __construct($map) {
		if (is_array($map)) {
			$this->map = $map;
		}
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

