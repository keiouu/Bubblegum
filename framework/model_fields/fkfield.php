<?php
/*
 * Tikapot Foreign Key Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");
require_once(home_dir . "framework/form_fields/init.php");
require_once(home_dir . "framework/utils.php");
require_once(home_dir . "framework/model.php");

class FKValidationException extends Exception { }

class FKField extends ModelField implements ModelInterface
{
	private static $string_cache = array();
	protected static $db_type = "varchar";
	protected $_obj, $_model, $_class, $_override_db_type, $valid = false;
	
	public function __construct($model = "", $override_db_type = null) {
		parent::__construct();
		$this->_obj = Null;
		$this->_model = $model;
		$this->_override_db_type = $override_db_type;
		$this->value = null;
		$this->_set_model($model);
	}
	
	public function db_create_query($db, $name, $table_name) {
		$obj = new $this->_class();
		$ref_str = " references " . $obj->get_table_name() . "(".$obj->_pk().")";
		return parent::db_create_query($db, $name, $table_name) . ($db->get_type() == "psql" ? $ref_str : "");
	}
	
	protected function _clean() {
		$this->_obj = Null;
		$this->value = null;
		$this->valid = false;
	}
	
	protected function _set_model($model) {
		$this->_model = $model;
		if ($this->_model !== "") {
			$this->_class = $this->load_class($this->_model);
			$this->valid = $this->_class !== null;
		}
	}
	
	public function get_formfield($name) {
		return new FKFormField($name, $this->_model, $this->grab_object());
	}
	
	public function is_set() {
		return $this->value !== null;
	}
	
	public function is_valid() {
		return $this->valid;
	}
	
	private function load_class($model_str) {
		list($app, $n, $class) = partition($model_str, '.');
		
		if (class_exists($class))
			return $class;
		
		/*
		 * Class is in the format: appname.modelName
		 * We must scan app paths for the app, then import models.php.
		 * Hopefully, $class will then exist
		 * TODO - autoload?
		 */
		global $app_paths;
		$test_paths = $app_paths;
		if (!in_array("framework", $test_paths))
			$test_paths[] = "framework";
		foreach ($test_paths as $app_path) {
			$path = home_dir . $app_path;
			if ($app !== "framework")
				$path .= '/' . $app;
			$path .= "/models.php";
			if (is_file($path)) {
				include_once($path);
				break;
			}
		}
		
		if (class_exists($class))
			return $class;
		
		console_warn($GLOBALS['i18n']['framework']["error1"] . " '" . $model_str . "' " . $GLOBALS['i18n']['framework']["fielderr5"]);
		return false;
	}
	
	protected function grab_object() {
		if (!$this->valid)
			return null;
		
		if ($this->is_set() && isset($this->_obj))
			return $this->_obj;
		if ($this->value !== null && $this->value !== 0) {
			try {
				return call_user_func(array($this->_class, 'get_or_ignore'), array("pk" => $this->value));
			} catch (Exception $e) {
				return null;
			}
		}
		return new $this->_class();
	}
	
	private function check_obj() {
		if ($this->valid && $this->_obj === Null)
			$this->_obj = $this->grab_object();
	}
	
	public function set_value($value) {
		if (is_object($value))
			$value = $value->pk;
		parent::set_value(($value === "0" || $value === 0) ? null : $value);
	}
	
	public function get_value() {
		$this->check_obj();
		return $this->is_set() ? $this->get_object() : null;
	}
	
	public function get_form_value() {
		$this->check_obj();
		$obj = $this->get_object();
		return $this->is_set() && $obj !== null ? $obj->pk : "null";
	}
	
	public function __toString() {
		$cache_string = $this->_model . "|" . $this->value;
		if (isset(FKField::$string_cache[$cache_string])) {
			return FKField::$string_cache[$cache_string];
		}
		$this->check_obj();
		$string = "" . ($this->value === null || $this->_obj === null ? "-" : $this->value);
		if ($this->is_set() && isset($this->_obj) && method_exists($this->_obj, "__toString"))
			$string = $this->_obj->__toString();
		FKField::$string_cache[$cache_string] = $string;
		return $string;
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		if (is_object($val))
			$val = $val->pk;
		return ($val !== null) ? $db->escape_string($val) : "0";
	}
	
	public function get_object() {
		$this->check_obj();
		return $this->_obj;
	}
	
	public function get_class() {
		return $this->_class;
	}
	
	public function get_model_string() {
		return $this->_model;
	}
	
	public function get_db_type() {
		if ($this->_override_db_type !== null)
			return $this->_override_db_type;
		$db_type = static::$db_type;
		if ($this->_class) {
			$obj = new $this->_class();
			$db_type = $obj->get_field("pk")->get_db_type();
		}
		return $db_type;
	}
	
	/* This recieves pre-save signal from it's model. */
	public function pre_save($model, $update) {
		// Save our model and set this db value to it's ID
		if ($this->is_set() && isset($this->_obj) && strlen($this->value) === 0) {
			$this->value = $this->_obj->save();
		}
	}
	
	public function __get($name) {
		$this->check_obj();
		if ($this->valid && isset($this->_obj->$name))
			return $this->_obj->$name;
	}
	
	public function __set($name, $value) {
		$this->check_obj();
		if ($this->valid && isset($this->_obj->$name))
			$this->_obj->$name = $value;
	}
	
	public function __call($name, $args) {
		$this->check_obj();
		if ($this->valid && method_exists($this->_obj, $name))
			return call_user_func_array(array($this->_obj, $name), $args);
	}
	
	public function __isset($name) {
		$this->check_obj();
		return $this->valid && isset($this->_obj->$name);
	}
	
	public function __unset($name) {
		$this->check_obj();
		if ($this->valid)
			unset($this->_obj->$name);
	}
	
	public function validate($val = NULL) {
		return $this->valid;
	}
	
	public function has_field($name) {
		if ($this->_obj === NULL)
			$this->_obj = $this->grab_object();
		return $this->_obj && $this->_obj->has_field($name);
	}
	
	public function get_field($name) {
		if ($this->_obj === NULL)
			$this->_obj = $this->grab_object();
		if ($this->_obj)
			return $this->_obj->$name;
		return null;
	}
	
	public function set_field($name, $value){
		if ($this->_obj === NULL)
			$this->_obj = $this->grab_object();
		if ($this->_obj)
			$this->_obj->$name = $value;
	}
	
	public function reset_field($name) {
		if ($this->_obj === NULL)
			$this->_obj = $this->grab_object();
		if ($this->_obj)
			unset($this->_obj->$name);
	}
	
	public function model_save() {
		if ($this->_obj === NULL)
			$this->_obj = $this->grab_object();
		if ($this->_obj) {
			$this->value = $this->_obj->save();
		}
	}
	
	public function hasRelation($model) {
		list($app, $n, $class) = partition($this->_model, '.');
		return $model == $class;
	}
}

?>

