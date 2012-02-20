<?php
/*
 * Tikapot Text Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");

class TextField extends CharField
{
	protected static $db_type = "TEXT";
	
	public function __construct($default = "", $_extra = "") {
		parent::__construct(0, $default, $_extra);
	}
	
	public function get_formfield($name) {
		return new TextFormField($name, $this->get_value());
	}
	
	public function validate() {
		// Not much to validate...
		return True;
	}
}

?>
