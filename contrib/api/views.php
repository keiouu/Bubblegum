<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");

class JSONView extends View
{
	private $_secure;
	
	public function __construct($url, $secure = false) {
		$this->_secure = $secure;
		parent::__construct($url);
	}

	public function setup($request, $args) {
		return (($this->is_secure() && $request->user->logged_in()) || !$this->is_secure()) && parent::setup($request, $args);
	}
	
	public function render($request, $args) {
		if (!isset($request->dataset))
			die('{"error":"No Result!"}');
		return json_encode($request->dataset);
	}
	
	public function is_secure() {
		return $this->_secure;
	}
}

class APISearchView extends JSONView
{
	private $_object, $_action;
	
	public function __construct($url, $object, $action, $secure = false) {
		$this->_object = $object;
		$this->_action = $action;
		parent::__construct($url, $secure);
	}

	public function setup($request, $args) {
		// Get all args, match with model's fields and do the action
		$query = array();
		foreach ($this->_object->get_fields() as $name => $field) {
			if (isset($request->get[$name])) {
				$query[$name] = $request->get[$name];
			}
		}
		
		$action = $this->_action;
		$result = $this->_object->$action($query);
		$request->dataset = array();
		foreach ($result as $object) {
			$array = array();
			foreach ($object->get_fields() as $oname => $ofield) {
				$array[$oname] = $ofield->get_form_value();
			}
			
			$request->dataset[] = $array;
		}
		
		return parent::setup($request, $args);
	}
}
?>

