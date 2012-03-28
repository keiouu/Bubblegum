<?php
/*
 * Tikapot Boolean Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");

class BooleanField extends ModelField
{
	protected static $db_type = "boolean";
	
	public function __construct($default = false) {
		parent::__construct($default);
	}
	
	public function get_value() {
		return $this->value === true || strtolower($this->value) === 'true' || strtolower($this->value) === "t" || $this->value === "1" || $this->value === 1;
	}
	
	public function __toString() {
		return $this->get_value() ? "True" : "False";
	}
	
	public function get_formfield($name) {
		return new CheckedFormField($name, $this->get_value());
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val === NULL) ? $this->get_value() : $val;
		return ($val) ? "true" : "false";
	}

	public function validate($val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		$valid = $val === true || $val === false || $val === 0 || $val === 1 || $val === NULL || strtolower($val) === 'true' || strtolower($val) === "t" || $val === "1" || strtolower($val) === 'false' || strtolower($val) === "f" || $val === "0";
		if (!$valid)
			array_push($this->errors, $GLOBALS['i18n']['framework']["fielderr1"] . " " . $val);
		return $valid;
	}
}

?>

