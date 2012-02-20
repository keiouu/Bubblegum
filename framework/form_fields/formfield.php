<?php
/*
 * Tikapot Form Field
 *
 */

require_once(home_dir . "framework/utils.php");

abstract class FormField
{
	protected $name, $value, $options, $error, $helptext;
	
	public function __construct($name, $initial_value = "", $options = array()) {
		$this->name = $name;
		$this->value = $initial_value;
		$this->options = $options;
		$this->error = "";
		$this->helptext = isset($options['helptext']) ?  $options['helptext'] : "";
	}
	
	public function validate($base_id, $safe_name) {
		return true;
	}
	
	public function set_name($val) {
		$this->name = $val;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function set_value($val) {
		$this->value = $val;
	}
	
	public function get_value() {
		return $this->value;
	}
	
	// Used for Forms
	public function get_display_value() {
		return $this->value;
	}
	
	public function get_type() { return ""; }
	
	public function set_error($val) {
		$this->error = $val;
	}
	
	public function has_error() {
		return $this->error != "";
	}
	
	public function get_error() {
		return $this->error;
	}
	
	public function set_helptext($val) {
		$this->helptext = $val;
	}
	
	public function has_helptext() {
		return $this->helptext != "";
	}
	
	public function get_helptext() {
		return $this->helptext;
	}
	
	public function get_placeholder() {
		return isset($this->options['placeholder']) ? $this->options['placeholder'] : "";
	}
	
	public function get_extras() {
		return isset($this->options['extra']) ? $this->options['extra'] : "";
	}
	
	public function get_options($key = "") {
		if ($key !== "")
			return isset($this->options[$key]) ? $this->options[$key] : "";
		return $this->options;
	}
	
	public function get_error_html($base_id, $safe_name) {
		return $this->error;
	}
	
	public function get_field_id($base_id, $safe_name) {
		return $base_id . '_' . $safe_name;
	}
	
	public function claim_own($my_name, $field_name, $field_value) {
		return false;
	}
	
	public function get_label($base_id, $safe_name) {
		$field_id = $this->get_field_id($base_id, $safe_name);
		if ($this->get_type() !== "hidden")
			return '<label for="'.$field_id.'">'.prettify($this->name).'</label>';
		return '';
	}
	
	protected function get_field_class() {
		return get_class(new static($this->name));
	}
	
	protected function get_classes($safe_name, $classes = "") {
		$classes = trim($classes . ' ' .  $safe_name . '_field');
		$classes = trim($classes . ' ' . $this->get_options("classes"));
		$classes = trim($classes . ' ' . $this->get_field_class());
		return $classes;
	}
	
	public function get_input($base_id, $safe_name, $classes = "") {
		$ret = "";
		$field_id = $this->get_field_id($base_id, $safe_name);
		$ret .= '<input';
		if ($base_id !== "control")
			$ret .= ' id="'.$field_id.'"';
		$ret .= ' class="'.$this->get_classes($safe_name, $classes).'" type="'.$this->get_type().'" name="'.$field_id.'"';
		if (strlen($this->get_display_value()) > 0)
			$ret .= ' value="'.$this->get_display_value().'"';
		if ($this->get_placeholder() !== "")
			$ret .= ' placeholder="'.$this->get_placeholder().'"';
		if ($this->get_extras() !== "")
			$ret .= ' ' . $this->get_extras();
		$ret .= ' />';
		return $ret;
	}
	
	public function pre_postdata_load() {}
}
?>

