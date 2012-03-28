<?php
/*
 * Tikapot Numeric Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");

class NumericField extends ModelField
{
	protected static $db_type = "NUMERIC";
	private $precision = 0;
	
	public function __construct($default = "", $precision = "", $_extra = "") {
		parent::__construct($default, $_extra);
		$this->precision = $precision;
	}
	
	public function get_formfield($name) {
		return new NumberField($name, $this->get_value());
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		if (strlen($val) <= 0)
			return "0";
		return $db->escape_string($val);
	}

	public function validate($val = NULL) {
		$val = ($val === NULL) ? $this->get_value() : $val;
		if (strlen($this->precision) > 0) {
			$parts = split(',', $this->precision);
			if (count($parts) < 2 || !preg_match('/^\d+$/', $parts[0]) || !preg_match('/^\d+$/', $parts[1])) {
				array_push($this->errors, $GLOBALS['i18n']['framework']["fielderr9"]);
				return False;
			}
		}
		if (strlen($val) > 0 && !is_numeric($val)) {
			array_push($this->errors, $GLOBALS['i18n']['framework']["fielderr10"]);
			return False;
		}
		return True;
	}
	
	public function db_create_query($db, $name, $table_name) {
		$extra = "";
		if (strlen($extra) > 0)
			$extra = ' ' . $extra;
		if ($this->precision !== "")
			$extra .= " (" . $this->precision . ")";
		if (strlen($this->default_value) > 0)
			$extra .= " DEFAULT '" . $this->default_value . "'";
		if (strlen($this->_extra) > 0)
			$extra .= ' ' . $this->_extra;
		return "\"" . $name . "\" " . $this::$db_type . $extra;
	}
}

?>
