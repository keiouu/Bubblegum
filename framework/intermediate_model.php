<?php
/*
 * Tikapot Model System - Intermediate Models (Occuring in M2M Relations)
 *
 */

require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "framework/model_query.php");

class InvalidOperationException extends Exception {}

class IntermediateModel extends Model
{
	protected $db_name = "", $link_model = "", $parent;
	
	public function __construct($name, $parent, $linkModel) {
		parent::__construct();
		$this->add_field("id", new KeyField());
		$this->add_field("link", new FKField($linkModel));
		$this->pk = $parent->pk;
		$this->parent = $parent;
		$this->link_model = $linkModel;
		$this->set_table_name($name);
	}
	
	public function set_table_name($name) {
		$this->db_name = $name;
	}
	
	public function get_table_name() {
		return $this->db_name;
	}
	
	public function temp_clone_link() {
		$class = $this->get_field("link")->get_class();
		return $class::get_temp_instance();
	}
	
	/*
	 * Returns a temporary clone of this object for use in model queries
	 */
	public function temp_clone() {
		return static::get_temp_instance($this->get_table_name(), $this->parent, $this->link_model);
	}
	
	public function add($obj) {
		$oi = new static($this->get_table_name(), $this->parent, $this->link_model);
		$obj->save();
		$oi->link = $obj->pk;
		$oi->save();
		return $oi;
	}
	
	
	public static function get_temp_instance($name, $parent, $linkModel) {
		$obj = new static($name, $parent, $linkModel);
		$obj->setValid(False);
		return $obj;
	}
	
	public static function objects($name, $parent, $linkModel) {
		return new ModelQuery(static::get_temp_instance($name, $parent, $linkModel));
	}
	
	public static function find($query) 		{ throw new InvalidOperationException(); }
	public static function get($arg = 0) 		{ throw new InvalidOperationException(); }
	public static function get_or_create($arg = 0) 	{ throw new InvalidOperationException(); }
	public static function create($args = array()) 	{ throw new InvalidOperationException(); }
	
	public function validate() {
		return $this->link->validate();
	}
}
?>

