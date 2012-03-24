<?php
/*
 * Tikapot Char Field
 *
 */

require_once(home_dir . "framework/model_fields/charfield.php");
require_once(home_dir . "framework/form_fields/init.php");

class PasswordField extends CharField
{
	public static function encode($password) {
		$salted = ConfigManager::get('password_salt', "") . $password . ConfigManager::get('password_salt2', "");
		return hash("sha512", $salted);
	}
	
	public function sql_value($db, $value = NULL) {
		$value = ($value === NULL) ? $this->value : $value;
		return parent::sql_value($db, PasswordField::encode($value));
	}
	
	public function get_form_value() {
		return "";
	}
	
	public function get_formfield($name) {
		return new PasswordFormField($name, $this->get_value(), array("extra" => 'maxlength="'.$this->max_length.'"'));
	}
}

?>
