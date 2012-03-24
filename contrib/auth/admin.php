<?php
/*
 * Tikapot Auth Admin Registration
 *
 */

require_once(home_dir . "framework/forms.php");
require_once(home_dir . "framework/form_fields/formfields.php");
require_once(home_dir . "contrib/admin/core.php");
require_once(home_dir . "contrib/auth/models.php");

class GroupsPanel extends AdminIndexPanel
{	
	public function render($request) {
		// Check we can add users to groups
		if (!$request->user->has_permission("user_management"))
			return "";
	
		$form = new Form(array(
			new Fieldset("", array(
				"group" => SelectFormField::from_model("Group", new User_Group()),
				"user" => SelectFormField::from_model("User", new User()),
			))
		), $request->fullPath . "?group_panel=1");
		
		if (isset($request->get['group_panel'])) {
			if ($form->load_post_data($request->post)) {
				$group = User_Group::get_or_ignore(array("pk" => $form->group));
				$user = User::get_or_ignore(array("pk" => $form->user));
				if ($group && $user) {
					list($obj, $created) = User_Group_Link::get_or_create(array(
						"group" => $group,
						"user" => $user,
					));
					if ($created)
						$request->message($GLOBALS['i18n']['auth']['groups_admin_panel1'], "success");
					else
						$request->message($GLOBALS['i18n']['auth']['groups_admin_panel2'], "warning");
				} else {
					if (!$user)
						$request->message($GLOBALS['i18n']['auth']['groups_admin_panel3'], "error");
					if (!$group)
						$request->message($GLOBALS['i18n']['auth']['groups_admin_panel4'], "error");
				}
				$form->clear_data();
			}
		}
		
		ob_start();
		$form->display();
		return "<p>Use the form below to add a user to a group.</p><br />" . ob_get_clean();
	}
}
AdminManager::register_panel(new GroupsPanel($GLOBALS['i18n']['auth']['groups_title']));

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
AdminModel::register("auth", new User_Group());
AdminModel::register("auth", new User_Group_Link());
AdminModel::register("auth", new Auth_Permission());
AdminModel::register("auth", new Auth_Permission_Link());
?>

