<?php
/*
 * Tikapot Choice Field
 *
 */

require_once(home_dir . "framework/model_fields/charfield.php");
require_once(home_dir . "framework/form_fields/init.php");

class ChoiceField extends CharField implements ArrayAccess
{
	private $choices;
	
	public function __construct($choices, $default = "", $max_length = 0, $_extra = "") {
		if (!is_array($choices))
			throw new Exception($GLOBALS["i18n"]["fielderr12"]);
		
		if ($max_length === 0) {
			foreach ($choices as $val => $choice) {
				if (strlen($val) + 1 > $max_length)
					$max_length = strlen($val) + 1;
			}
		}
		
		parent::__construct($max_length, $default, $_extra);
		$this->choices = $choices;
	}
	
	public function offsetSet($offset, $value) {
		throw new Exception($GLOBALS["i18n"]["fielderr19"]);
	}
	
	public function offsetExists($offset) {
		foreach ($this->choices as $val => $choice)
			if ($offset === $choice)
				return true;
		return false;
	}
	
	public function offsetUnset($offset) {
		foreach ($this->choices as $val => $choice)
			if ($offset === $choice)
				unset($this->choices[$val]);
	}
	
	public function offsetGet($offset) {
		foreach ($this->choices as $val => $choice)
			if ($offset === $choice)
				return $val;
		return null;
	}
	
	public function __toString() {
		$value = $this->get_value();
		if (isset($this->choices[$value]))
			return $this->choices[$value];
		return $value;
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		foreach ($this->choices as $key => $choice)
			if ($val == $choice)
				return parent::sql_value($db, $key);
		return parent::sql_value($db, $val);
	}
	
	public function get_choices() {
		return $this->choices;
	}
	
	public function set_value($value) {
		if (strlen(trim($value)) === 0)
			$value = $this->default_value;
		foreach ($this->choices as $val => $choice) {
			if ($value == $val)
				return parent::set_value($value);
			if ($value == $choice) // In case they go by the right side of the array!
				return parent::set_value($val);
		}
		console_log($GLOBALS["i18n"]["fielderr14"] . " " . $value);
	}
	
	public function get_formfield($name) {
		return new SelectFormField($name, $this->choices, $this->get_value(), array("extra" => 'maxlength="'.$this->max_length.'"'));
	}
	
	public function validate($val = NULL) {
		$mval = ($val === NULL) ? $this->get_value() : $val;
		foreach ($this->choices as $val => $choice)
			if ($mval == $val || $mval == $choice)
				return parent::validate($val);
		array_push($this->errors, $GLOBALS["i18n"]["fielderr13"] . " " . $mval);
		return false;
	}
}

?>
