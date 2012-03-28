<?php
/*
 * Tikapot Fieldset
 *
 */

class Fieldset implements ArrayAccess, Iterator
{
	protected $fields = array(), $legend = "", $id_override = "";
	
	public function __construct($legend, $fields, $id_override = "") {
		$this->legend = $legend;
		$this->id_override = $id_override;
		$this->load($fields);
	}
	
	public function get_id($default) {
		return (strlen($this->id_override) > 0) ? $this->id_override : $default;
	}
	
	public function get_fields() {
		return $this->fields;
	}
	
	public function get_legend() {
		return $this->legend;
	}
	
	public function add_item($name, $field) {
		$this->fields[$name] = $field;
	}
	
	public function load($arr) {
		foreach($arr as $name => $field) {
			$this->add_item($name, $field);
		}
	}
	
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->fields[] = $value;
		} else {
			$this->fields[$offset] = $value;
		}
	}
	
	public function offsetExists($offset) {
		return isset($this->fields[$offset]);
	}
	
	public function offsetUnset($offset) {
		unset($this->fields[$offset]);
	}
	
	public function offsetGet($offset) {
		return isset($this->fields[$offset]) ? $this->fields[$offset] : null;
	}

    public function rewind() {
        reset($this->fields);
    }
  
    public function current() {
        return current($this->fields);
    }
  
    public function key() {
        return key($this->fields);
    }
  
    public function next() {
        return next($this->fields);
    }
  
    public function valid() {
        $key = key($this->fields);
        return ($key !== NULL && $key !== FALSE);
    }
}

