<?php
/*
 * Tikapot Admin Filters
 */

require_once(home_dir . "framework/utils.php");

class AdminFilter
{
	protected $name, $options, $default_value;
	
	public function __construct($name, $options, $default_value) {
		$this->name = $name;
		$this->options = $options;
		asort($this->options);
		$this->default_value = $default_value;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_options() {
		return $this->options;
	}
	
	public function get_default_value() {
		return $this->default_value;
	}
	
	public function render($request) {
		// Are we in the request?
		if (isset($request->get['_' . $this->name]) && strlen($request->get['_' . $this->name]) > 0)
			$initial_key = $request->get['_' . $this->name];
		if (!isset($initial_key) && $this->default_value !== "")
			$initial_key = $this->default_value;
		
		// Render!
		$data = '<p>'.prettify($this->name).'</p>';
		$data .= '<select name="'.$this->name.'"><option value="">------------------</option>';
		foreach ($this->options as $key => $value) {
			$value = trim($value);
			if (strlen($value) > 0)
				$data .= '<option value="'.$key.'" '.(isset($initial_key) && $initial_key == $key ? 'selected="selected"' : '').'>'.$value.'</option>';
		}
		$data .= '</select>';
		return $data;
	}
}
?>
