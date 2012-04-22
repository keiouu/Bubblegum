<?php
/*
 * Tikapot Integer Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");

class IntegerField extends ModelField
{
	protected static $db_type = "INT";
	private $max_length = 0, $auto_increment = False;
	
	public function __construct($max_length = 50, $default = 0, $auto_increment = False, $_extra = "") {
		parent::__construct($default, $_extra);
		$this->max_length = ($max_length > 0) ? $max_length : 50;
		$this->auto_increment = $auto_increment;
		$this->hide_from_query = $auto_increment;
	}
	
	public function get_formfield($name) {
		return new NumberFormField($name, $this->get_value());
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		if (strlen($val) <= 0)
			return 0;
		return intval($val);
	}

	public function validate($val = NULL) {
		$val = ($val === NULL) ? $this->get_value() : $val;
		$regex = "/^(\d{0,".$this->max_length."})$/";
		$valid = preg_match($regex, $val) == 1; // These == 1 are not needed but clarify test results
		if (!$valid)
			array_push($this->errors, $GLOBALS['i18n']['framework']["fielderr6"] . " " . $val);
		return $valid && (strpos($val, ".") == False);
	}
	
	protected function sequence_name($db, $name, $table_name) {
		return $db->escape_string($table_name."_".$name."_seq");
	}
	
	public function db_create_query($db, $name, $table_name) {
		$extra = "";
		if (strlen($extra) > 0)
			$extra = ' ' . $extra;
		if ($db->get_type() != "psql" && $this->max_length > 0)
			$extra .= " (" . $this->max_length . ")";
		if (!$this->auto_increment && strlen($this->default_value) > 0)
			$extra .= " DEFAULT '" . $this->default_value . "'";
		if ($this->auto_increment) {
			if ($db->get_type() == "mysql")
				$extra .= " AUTO_INCREMENT";
			if ($db->get_type() == "psql")
				$extra .= " DEFAULT nextval('".$this->sequence_name($db, $name, $table_name)."')";
		}
		if (strlen($this->_extra) > 0)
			$extra .= ' ' . $this->_extra;
		return "\"" . $name . "\" " . $this::$db_type . $extra;
	}
	
	public function pre_model_create($db, $name, $table_name) {
		if ($db->get_type() == "psql" && $this->auto_increment) {
			$seq = $this->sequence_name($db, $name, $table_name);
			$db->query('DROP SEQUENCE IF EXISTS '.$seq.';');
			return "CREATE SEQUENCE ".$seq.";";
		}
		return "";
	}
}

?>
