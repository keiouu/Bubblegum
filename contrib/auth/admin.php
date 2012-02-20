<?php
/*
 * Tikapot Auth Admin Registration
 *
 */

require_once(home_dir . "framework/forms.php");
require_once(home_dir . "framework/form_fields/formfields.php");
require_once(home_dir . "contrib/admin/core.php");
require_once(home_dir . "contrib/auth/models.php");

class AuthPasswordFormField extends PasswordFormField
{
	public function get_value() {
		return User::encode($this->value);
	}
	
	public function get_real_value() {
		return $this->value;
	}
	
	public function get_display_value() {
		return "";
	}
}

class AuthForm extends Form
{
	public function __construct($fieldset = NULL) {
		if ($fieldset === NULL)
			$fieldset = $this->get_auth_fieldset();
		parent::__construct(array($fieldset));
	}
	
	protected function get_auth_fieldset($password_placeholder = "") {
		$dummy = new User();
		return new Fieldset("User", array(
				"username" => new CharFormField("Username", ""),
				"password" => new AuthPasswordFormField("Password", "", array("placeholder" => $password_placeholder, "extra" => 'autocomplete = "off"')),
				"email" => new EmailFormField("Email", ""),
				"status" => new SelectFormField("Status", $dummy->_status->get_choices(), ""),
		));
	}
}

class AuthEditForm extends AuthForm
{
	public function __construct() {
		parent::__construct($this->get_auth_fieldset("Leave blank to ignore"));
	}
	
	public function save($model, $request) {
		$new = !$model->fromDB();
		$password = $model->password;
		
		// Save it
		$result = parent::save($model, $request);
		
		// If the object isnt new and we didnt specify a new password, reset the password
		if (!$new && strlen($this->_password->get_real_value()) == 0) {
			$model->password = $password;
			$model->save();
		}
		
		return $result;
	}
}

AdminModel::register("auth", new User(), new AuthForm(), new AuthEditForm(), array("id", "username", "email", "status", "created", "last_login"), array("username"));
AdminModel::register("auth", new User_Permission());
AdminModel::register("auth", new Permission());
?>

