<?php
/*
 * Tikapot Admin Model
 */

require_once(home_dir . "framework/fieldset.php");
require_once(home_dir . "contrib/admin/core.php");
require_once(home_dir . "contrib/admin/filters.php");

class AdminModel
{
	protected $app, $model, $add_form, $edit_form, $headings, $linked_headings, $filters, $actions, $model_page, $add_page, $edit_page;
	
	public function __construct($app, $model, $add_form = null, $edit_form = null, $headings = array(), $linked_headings = array(), $filters = array(), $actions = array()) {
		$this->app = $app;
		$this->model = $model;
		
		// Setup Forms
		if ($add_form === null) {
			$add_form = $model->get_form();
		}
		$this->add_form = $add_form;
		$this->edit_form = $edit_form === null ? $add_form : $edit_form;
		
		// Setup Headings
		$this->headings = $headings;
		if (!is_array($this->headings) || count($this->headings) === 0) {
			$this->headings = array();
			$fields = $this->model->get_fields();
			foreach($fields as $name => $field) {
				$this->headings[] = $name;
			}
		}
		
		// Setup Linked Headings
		$this->linked_headings = $linked_headings;
		if (!is_array($this->linked_headings) || count($this->linked_headings) === 0) {
			$this->linked_headings = array($this->model->get_pk_name());
		}
		
		// Setup Filters
		$this->filters = $filters;
		if (!is_array($this->filters) || count($this->filters) === 0) {
			$this->filters = array();
			$fields = $this->model->get_fields();
			foreach($fields as $name => $field) {
				$form_field = $field->get_formfield("");
				$class = get_class($form_field);
				if ($class === "CheckedFormField" || $class === "SelectFormField" || $class === "FKFormField")
					$this->filters[$name] = "";
			}
		}
		
		$this->actions = $actions;
		
		$this->set_model_page(home_dir . "contrib/admin/templates/model.php");
		$this->set_add_page(home_dir . "contrib/admin/templates/newmodel.php");
		$this->set_edit_page(home_dir . "contrib/admin/templates/editmodel.php");
		
		// Add me to the admin manager
		AdminManager::add($app, $this);
	}
	
	public function get_model_page() {
		return $this->model_page;
	}
	
	public function get_add_page() {
		return $this->add_page;
	}
	
	public function get_edit_page() {
		return $this->edit_page;
	}
	
	public function set_model_page($model_page) {
		$this->model_page = $model_page;
	}
	
	public function set_add_page($add_page) {
		$this->add_page = $add_page;
	}
	
	public function set_edit_page($edit_page) {
		$this->edit_page = $edit_page;
	}
	
	public function get_app() {
		return $this->app;
	}
	
	public function get_model() {
		return $this->model;
	}
	
	public function addAction($action) {
		$this->actions[] = $action;
	}
	
	public function get_actions() {
		return $this->actions;
	}
	
	public function get_filters() {
		$fields = $this->model->get_fields();
		$filters = array();
		foreach ($this->filters as $name => $default_value) {
			if (array_key_exists($name, $fields) && in_array($name, $this->headings)) {
				$options = array();
				$dataset = $this->get_dataset();
				$data = $dataset->get_data();
				foreach ($data as $obj) {
					$value = $dataset->get_value($obj, $name);
					if (!in_array("" . $value, $options))
						$options[$value->get_form_value()] = "" . $value;
				}
				$filters[] = new AdminFilter($name, $options, $default_value);
			}
		}
		return $filters;
	}
	
	public function get_add_form($request) {
		return $this->add_form;
	}
	
	public function get_edit_form($request) {
		return $this->edit_form;
	}
	
	public function get_modelname() {
		return $this->model->_display_name();
	}
	
	public function get_headings() {
		return $this->headings;
	}
	
	public function get_linked_headings() {
		return $this->linked_headings;
	}
	
	public function get_dataset($filter_values = array(), $order = array()) {
		return new DataSet($this->model, $this->model->objects()->filter($filter_values)->order_by($order), $this->get_headings(), $this->get_linked_headings());
	}
	
	public static function register($app = null, $model = null, $add_form = null, $edit_form = null, $headings = array(), $linked_headings = array(), $filters = array(), $actions = array()) {
		$obj = new AdminModel($app, $model, $add_form, $edit_form, $headings, $linked_headings, $filters, $actions);
		return $obj;
	}
}

/**
 * An admin model for providing custom admin pages
 */
class AdvancedAdminModel
{
	/**
	 * Construct
	 * 
	 * @param string $app The name of the application
	 * @param Object $model An instance of the model to register
	 * @param string $model_page The filename of a model page
	 * @param string $add_page The filename of a model add page
	 * @param string $edit_page The filename of a model edit page
	 */
	public function __construct($app, $model, $model_page = "", $add_page = "", $edit_page = "") {
		parent::__construct($app, $model);
		$this->set_model_page($model_page);
		$this->set_add_page($add_page);
		$this->set_edit_page($edit_page);
	}
}
?>
