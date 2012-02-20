<?php
/*
 * Tikapot Auth App
 *
 */

require_once(home_dir . "framework/form_printers.php");

class LoginFormPrinter extends HTMLFormPrinter
{
	public function run($form, $action = "", $method = "", $submit_text = "") {
		return parent::run($form, $action, $method, strlen($submit_text > 0) ? $submit_text : "Login");
	}
}

class RegistrationFormPrinter extends HTMLFormPrinter
{
	public function run($form, $action = "", $method = "", $submit_text = "") {
		return parent::run($form, $action, $method, strlen($submit_text > 0) ? $submit_text : "Register");
	}
}
?>
