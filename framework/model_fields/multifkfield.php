<?php
/*
 * Tikapot Foreign Key Field
 *
 */

require_once(home_dir . "framework/model_fields/fkfield.php");
require_once(home_dir . "framework/utils.php");

class MultiFKField extends FKField
{
	protected static $db_type = "varchar";
	private $_models = array();
	
	public function __construct() {
		$arg_list = func_get_args();
		foreach ($arg_list as $arg) {
			list($app, $model) = explode(".", $arg);
			$this->_models[$model] = $app;
		}
		return parent::__construct();
	}
	
	public function __toString() {
		if (strpos($this->value, "|") === FALSE)
			return "";
		list($class, $pk) = explode("|", $this->value);
		if (isset($this->_models[$class]) && $this->load_class($this->_models[$class] . "." . $class) !== false) {
			$object = $class::get_or_ignore(array("pk" => $pk));
			if ($object)
				return "" . $object;
		}
		return "";
	}
	
	public function get_db_type() {
		return MultiFKField::$db_type;
	}
	
	/* Used by the formfield to turn app.model into a model instance */
	public function _determine_object($string) {
		if ($this->load_class($string) !== false) {
   		list($app, $model) = explode(".", $string);
			return new $model();
		}
	}
	
	public function set_value($value) {
		if (is_object($value)) {
			$class = get_class($value);
			if (!isset($this->_models[$class]))
				return console_log($GLOBALS['i18n']['framework']['mfk_err1'] . $class);
			return parent::set_value($class."|".$value->pk);
		}
		// Sanity Checks
		if (strpos($value, "|") === FALSE)
			return console_log($GLOBALS['i18n']['framework']['mfk_err2'] . $value);
		list($class, $pk) = explode("|", $value);
		if (!isset($this->_models[$class]))
			return console_log($GLOBALS['i18n']['framework']['mfk_err1'] . $class);
		return parent::set_value($value);
	}
	
	public function get_value() {
		if (!strpos($this->value, "|"))
			return null;
		list($class, $pk) = explode("|", $this->value);
		$value = $this->value;
		$this->_clean();
		$this->value = $value;
		$this->_set_model($this->_models[$class] . "." . $class);
		return $this->grab_object();
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		if (is_object($val))
			$val = $val->pk;
		return "'" . (($val !== null) ? $db->escape_string($val) : "0") . "'";
	}
	
	protected function grab_object() {
		if (!$this->is_valid())
			return null;
		if ($this->is_set() && isset($this->_obj))
			return $this->_obj;
		if ($this->value !== null && $this->value !== 0) {
			list($class, $pk) = explode("|", $this->value);
			try {
				return call_user_func(array($class, 'get'), array("pk" => $pk));
			} catch (Exception $e) {
				return null;
			}
		}
		return new $this->_class();
	}
	
	public function post_model_create($db, $name, $table_name) {
		return "";
	}
	
	public function db_create_query($db, $name, $table_name) {
		return "\"" . $name . "\" " . $this->get_db_type();
	}
	
	public function get_formfield($name) {
		return new MultiFKFormField($name, $this->_models, $this, $this->grab_object());
	}
	
	public function validate($val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		if (strpos($val, "|") === FALSE)
			return false;
		list($model, $pk) = explode("|", $val);
		return isset($this->_models[$model]);
	}
	
	public function relatesTo($model) {
		list($app, $n, $class) = partition($this->_model, '.');
		foreach ($this->_models as $m => $a) {
			if ($m == $class)
				return true;
		}
		return false;
	}
}
?>

