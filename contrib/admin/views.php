<?php
/*
 * Tikapot Admin App Views
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "framework/utils.php");
require_once(home_dir . "framework/forms.php");
require_once(home_dir . "framework/fieldset.php");
require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "contrib/auth/models.php");
require_once(home_dir . "contrib/admin/core.php");

// For externals
class AdminPermissionsView extends View
{
	public function setup($request, $args) {
		return $request->user->logged_in() && $request->user->has_permission("admin_site") && parent::setup($request, $args);
	}
}

class BaseAdminView extends TemplateView
{
	public function on_setup_failure($request, $args = array()) {
		$view = new TemplateView("/admin/failure/", home_dir . "contrib/admin/templates/permission.php");
		$view->setup($request, $args);
		print $view->pre_render($request, $args);
		print $view->render($request, $args);
		print $view->post_render($request, $args);
	}
}

class AdminView extends BaseAdminView
{
	public function setup($request, $args) {
		if ($request->user->logged_in() && !$request->user->has_permission("admin_site"))
			$request->message($request->i18n['admin_permissue'], "error");
		if (!$request->user->logged_in())
			header("Location: " . home_url . "admin/login/");
		return $request->user->logged_in() && $request->user->has_permission("admin_site") && parent::setup($request, $args);
	}
}

class AdminLoginView extends BaseAdminView
{
	public function setup($request, $args) {
		if ($request->user->logged_in() && $request->user->has_permission("admin_site")) {
			header("Location: " . home_url . "admin/");
			SignalManager::fire("admin_on_login", $request->user);
		}
		return true;
	}
}

class AdminRegisterView extends BaseAdminView
{
	public function setup($request, $args) {
		$request->admin_register_form = new Form(array(
			new Fieldset("", array(
				"admin_password" => new PasswordFormField($request->i18n["auth_apass"], "", array("placeholder"=>$request->i18n["auth_apass_holder"])),
				"email" => new EmailFormField($request->i18n["auth_email"], "", array("placeholder"=>$request->i18n["auth_email_holder"])),
				"password" => new PasswordFormField($request->i18n["auth_password"], "", array("placeholder"=>$request->i18n["auth_password_holder"])),
				"password2" => new PasswordFormField($request->i18n["auth_password2"], "", array("placeholder"=>$request->i18n["auth_password2_holder"])),
			)),
		), $request->fullPath, "POST");
		
		if (isset($request->post['submit_register'])) {
			$request->admin_register_form->load_post_data($request->post);
			$cfg_admin_pass = ConfigManager::get_or_except('admin_password');
			$frm_admin_pass = $request->admin_register_form->get_value("admin_password");
			if ($frm_admin_pass !== $cfg_admin_pass && sha1($frm_admin_pass) !== $cfg_admin_pass && md5($frm_admin_pass) !== $cfg_admin_pass) {
				$request->message($request->i18n["auth_err1"], "error");
				return true;
			}
			if (!$request->admin_register_form->get_value("password") == $request->admin_register_form->get_value("password2")) {
				$request->message($request->i18n["auth_err2"], "error");
				return true;
			}
			try {
				list($user, $code) = User::create_user($request->admin_register_form->get_value("email"), $request->admin_register_form->get_value("password"), $request->admin_register_form->get_value("email"), $request->user->_status['admin'], true);
				SignalManager::fire("admin_on_register", $user);
				$request->admin_register_form->clear_data();
				$request->message($request->i18n["auth_success"], "success");
				if ($code)
					$code->delete();
				@User::login($request, $request->admin_register_form->get_value("email"), $request->admin_register_form->get_value("password"));
				header("Location: " . home_url . "admin/");
				return false;
			} catch(Exception $e) { $request->message($e->getMessage(), "error"); }
		}
		
		return true;
	}
}

class AdminUpdateView extends AdminView
{
	public function setup($request, $args) {
		return $request->user->has_permission("tikapot_update") && parent::setup($request, $args);
	}
	
	public function render($request, $args) {
		SignalManager::fire("admin_on_update", $request);
		return parent::render($request, $args);
	}
}

class AdminConfigView extends AdminView
{
	public function setup($request, $args) {
		return $request->user->has_permission("tikapot_config_view") && parent::setup($request, $args);
	}
	
	public function render($request, $args) {
		SignalManager::fire("admin_on_config", $request);
		return parent::render($request, $args);
	}
}

class AdminUpgradeView extends AdminView
{
	public function setup($request, $args) {
		return $request->user->has_permission("tikapot_upgrade") && parent::setup($request, $args);
	}
	
	public function render($request, $args) {
		SignalManager::fire("admin_on_upgrade", $request);
		return parent::render($request, $args);
	}
}

class AdminAppView extends AdminView
{
	protected $name, $apps;
	
	public function __construct($url, $page, $name, $apps) {
		parent::__construct($url, $page, prettify($name));
		$this->name = $name;
		$this->apps = $apps;
	}
	
	public function setup($request, $args) {
		$request->app_name = prettify($this->name);
		$request->app_apps = $this->apps;
		return $request->user->has_permission("admin_site_app_" . $this->name) && parent::setup($request, $args);
	}
}

class AdminModelView extends AdminView
{
	protected $admin, $app, $model, $model_name, $app_url, $model_url;
	
	public function __construct($url, $page, $admin) {
		$this->app = $admin->get_app();
		$modelname = $admin->get_modelname();
		
		$this->app_url = home_url . "admin/" . $this->app . "/";
		$this->model_url = $this->app_url . $modelname . "/";
		$this->admin = $admin;
		$this->model = $admin->get_model();
		$this->model_name = $modelname;
		parent::__construct($url, $page, prettify($modelname));
	}
	
	public function setup($request, $args) {
		$request->admin = $this->admin;
		$request->app_url = $this->app_url;
		$request->model_url = $this->model_url;
		$request->app = prettify($this->app);
		$request->model = prettify($this->model_name);
		
		if (!$request->user->has_permission("admin_site_model_" . $this->model_name))
			return false;
		
		if (isset($request->get['delete'])) {
			$ids = explode(",", $request->get['delete']);
			foreach ($ids as $id) {
				try {
					$obj = $this->model->get(array("pk" => $id));
					SignalManager::fire("admin_on_delete", array($request->user, $obj));
					$obj->delete();
				} catch (Exception $e) {
				}
			}
		}
		
		$filters = array();
		$order = array();
		foreach ($request->get as $key => $val) {
			if (starts_with($key, "_") && strlen($val) > 0)
				$filters[substr($key, 1)] = $val;
			if (starts_with($key, "*"))
				$order[] = array(substr($key, 1), $val);
		}
		foreach($this->admin->get_filters() as $obj) {
			if (!isset($filters[$obj->get_name()]) && $obj->get_default_value() !== "") {
				$filters[$obj->get_name()] = $obj->get_default_value();
			}
		}
		$request->dataset = $this->admin->get_dataset($filters, $order);
		$request->pagination_limit = isset($request->get['limit']) ? $request->get['limit'] : 25;
		$request->current_page = isset($request->get['page']) ? $request->get['page'] : 1;
		$max_page = $request->dataset->get_pages($request->pagination_limit);
		$request->current_page = $request->current_page > $max_page ? $max_page : $request->current_page;
		return parent::setup($request, $args);
	}
}

class AdminAddModelView extends AdminModelView
{
	protected function model_create($request) {
		$success = $request->modelform->load_post_data($request->post);
		try {
			$obj = $request->modelform->save($this->model, $request);
		}
		catch (Exception $e) {
			$success = false;
			$request->message($GLOBALS['i18n']['admin_form_add_error']);
		}
		if ($success && $obj !== NULL) {
			SignalManager::fire("admin_on_create", array($request->user, $obj));
			$request->message($GLOBALS['i18n']['admin_model_add'], "success");
			if (!isset($request->post['submit_stay'])) {
				header("Location: " . $this->model_url);
				die();
			}
			$request->modelform->clear_data();
		}
	}
	
	public function setup($request, $args) {
		$request->admin_add = true;
		
		if (!$request->user->has_permission("admin_site_model_add_" . $this->model_name))
			return false;
		
		$request->modelform = $this->admin->get_add_form($request);
		
		// Do we have a model to create?
		if (isset($request->post['submit']) || isset($request->post['submit_stay'])) {
			$this->model_create($request);
		}
		
		return parent::setup($request, $args);
	}
}

class AdminEditModelView extends AdminModelView
{	
	protected function model_edit($request, $args) {
		$success = $request->modelform->load_post_data($request->post);
		try {
			$obj = $request->modelform->save($request->model_obj, $request);
		}
		catch (Exception $e) {
			$success = false;
			$request->message($GLOBALS['i18n']['admin_form_add_error']);
		}
		if ($success) {
			SignalManager::fire("admin_on_edit", array($request->user, $request->model_obj));
			$request->message($GLOBALS['i18n']['admin_model_edit'], "success");
			if (!isset($request->post['submit_stay'])) {
				header("Location: " . $this->model_url);
				die();
			}
			$request->modelform->clear_data();
			$request->modelform->load_model_data($request->model_obj);
		}
	}
	
	public function setup($request, $args) {
		$request->admin_edit = true;
		
		if (!$request->user->has_permission("admin_site_model_edit_" . $this->model_name))
			return false;
		
		$request->model_obj = $this->model->get(array("pk" => $args['pk']));
		$request->modelform = $this->admin->get_edit_form($request);
		$request->modelform->load_model_data($request->model_obj);
		
		// Do we have a model to create?
		if (isset($request->post['submit']) || isset($request->post['submit_stay'])) {
			$this->model_edit($request, $args);
		}
		
		return parent::setup($request, $args);
	}
}
?>

