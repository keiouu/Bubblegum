<?php
/*
 * Tikapot Model Field
 *
 */
 
class FieldValidationException extends Exception { }

abstract class ModelField
{
	protected static $db_type = "unknown";
	protected $default_value = "", $value = "", $model = NULL, $name = "", $set = False;
	public $errors = array(), $_extra = "", $hide_from_query = False;

	public function __construct($default = "", $_extra = "") {
		$this->default_value = $default;
		$this->value = $this->default_value;
		$this->_extra = $_extra;
	}
	
	public function __toString() {
		return "" . $this->value;
	}
	
	public abstract function get_formfield($name);
	
	public function is_set() {
		return $this->set;
	}
	
	public function setup($model, $name) {
		$this->set_model($model);
		$this->set_name($name);
	}
	
	public function set_value($value) {
		$this->set = True;
		$this->value = $value;
	}
	
	public function get_value() {
		return $this->value;
	}
	
	public function get_form_value() {
		return $this->get_value();
	}
	
	public function set_name($name) {
		$this->name = $name;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function set_model($model) {
		$this->model = $model;
	}
	
	public function get_model() {
		return $this->model;
	}
	
	public function get_db_type() {
		return static::$db_type;
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		return (strlen("" . $val)  > 0) ? $db->escape_string($val) : "NULL";
	}

	public function get_default() {
		return $this->default_value;
	}
	
	public function reset() {
		$this->value = $this->default_value;
	}
	
	public abstract function validate($val = NULL);

	public function db_create_query($db, $name, $table_name) {
		return "\"" . $name . "\" " . $this->get_db_type();
	}
	
	/* This allows subclasses to provide end-of-statement additions such as constraints */
	public function db_post_create_query($db, $name, $table_name) {
		return "";
	}
	
	/* This allows subclasses to provide extra, separate queries on createdb such as sequences. These are put before the create table query. */
	public function pre_model_create($db, $name, $table_name) {
		return "";
	}
	
	/* This allows subclasses to provide extra, separate queries on createdb such as sequences. These are put after the create table query. */
	public function post_model_create($db, $name, $table_name) {
		return "";
	}
	
	/* This recieves pre-save signal from it's model. */
	public function pre_save($model, $update) {}
	
	/* Is this a pk field?. */
	public function is_pk_field() { return false; }
	
	/* Could this have a relation to another model (e.g. FK Fields) */
	public function relatesTo($model) { return false; }
}

?>

