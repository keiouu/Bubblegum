<?php
/*
 * Tikapot Char Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");
require_once(home_dir . "framework/form_fields/init.php");

class CharField extends ModelField
{
	protected static $db_type = "VARCHAR";
	protected $max_length = 0;
	
	public function __construct($max_length = 0, $default = "", $_extra = "") {
		parent::__construct($default, $_extra);
		$this->max_length = $max_length;
	}
	
	public function get_formfield($name) {
		return new CharFormField($name, $this->get_value(), array("extra" => 'maxlength="'.$this->max_length.'"'));
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		if (strlen($val) <= 0)
			return "''";
		return "'" . $db->escape_string($val) . "'";
	}
	
	public function validate($val = NULL) {
		$val = ($val === NULL) ? $this->get_value() : $val;
		if ($this->max_length > 0 && strlen($val) > $this->max_length) {
			array_push($this->errors, $GLOBALS['i18n']['framework']["fielderr2"]);
			return False;
		}
		return True;
	}
	
	public function db_create_query($db, $name, $table_name) {
		$extra = "";
		if ($this->max_length > 0)
			$extra .= " (" . $this->max_length . ")";
		if (strlen($this->default_value) > 0)
			$extra .= " DEFAULT '" . $this->default_value . "'";
		if (strlen($this->_extra) > 0)
			$extra .= ' ' . $this->_extra;
		return "\"" . $name . "\" " . $this::$db_type . $extra;
	}
}

?>
