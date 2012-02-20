<?php
/*
 * Tikapot Many-To-Many Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");
require_once(home_dir . "framework/intermediate_model.php");
require_once(home_dir . "framework/model_query.php");

class M2MField extends ModelField
{
	protected $fmodel, $intermediate;
	
	public function __construct($model) {
		parent::__construct();
		$this->fmodel = $model;
	}
	
	public function get_formfield($name) {
		throw new Exception($GLOBALS["i18n"]["fielderr7"]);
	}
	
	protected function extrapolate_model() {
		return $this->intermediate->temp_clone_link();
	}
		
	protected function get_modelquery($query = array()) {
		if (!$this->value)
			return new ModelQuery($this->extrapolate_model(), $query);
		$built_link_ids = "(";
		$q = new ModelQuery($this->intermediate->temp_clone(), array("WHERE" => array("pk" => $this->value)));
		if ($q->count() <= 0)
			return new ModelQuery($this->extrapolate_model(), $query);
		foreach ($q->all() as $obj) {
			if (strlen($built_link_ids) > 1)
				$built_link_ids .= ",";
			$built_link_ids .= "" . $obj->pk;
		}
		$built_link_ids .= ")";
		$built_query = array("WHERE" => array("pk" => array("IN", $built_link_ids)));
		return new ModelQuery($this->extrapolate_model(), array_merge_recursive($built_query, $query));
	}
	
	public function setup($model, $name) {
		parent::setup($model, $name);
		$this->intermediate = new IntermediateModel($model->get_table_name() . "_" . $name, $model, $this->fmodel);
	}
	
	public function pre_save($model, $update) {
		$this->value = $this->intermediate->save();
	}
	
	public function get_value() {
		return $this;
	}
	
	public function validate() {
		return $this->intermediate->validate();
	}
	
	public function objects() {
		return $this->get_modelquery();
	}
	
	public function all() {
		return $this->objects();
	}
	
	public function find($query) {
		return $this->objects()->find($query);
	}
	
	public function exists($query) {
		return $this->find($query)->exists();
	}
	
	public function get($query) {
		$objs = $this->objects()->find($query);
		if ($objs->count() > 1)
			throw new ModelQueryException($GLOBALS["i18n"]["fielderr8"]);
		return $objs->get(0);
	}
	
	public function add($obj) {
		$this->intermediate->add($obj);
	}
	
	public function create($args) {
		$class = get_class($this->extrapolate_model());
		$obj = new $class();
		$obj->load_values($args);
		$obj->validate();
		$obj->save();
		$this->add($obj);
		return $obj;
	}
	
	public function get_or_create($args = 0) {
		if ($this->exists($args))
			return array($this->get($args), false);
		return array($this->create($args), true);
	}
	
	public function count() {
		return $this->objects()->count();
	}
}

?>

