<?php
/*
 * Tikapot Auth App
 *
 */

require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "framework/signal_manager.php");
require_once(home_dir . "framework/forms.php");
require_once(home_dir . "framework/form_fields/init.php");
require_once(home_dir . "contrib/auth/models.php");

SignalManager::register("auth_login", "auth_login_fail", "auth_pre_create", "auth_post_create");
ConfigManager::register_app_config("auth", "email_address", "you@example.com");
ConfigManager::register_app_config("auth", "cookie_timeout", 60 * 60 * 4 * 24 * 7);
ConfigManager::register_app_config("auth", "session_timeout", 60 * 60 * 4);

/* This checks the request to see if a user is trying to validate their email address */
function check_email_verification($request) {
	$request->email_verification = false;
	if (isset($request->get['verify_email'])) {
		$confirmationcodes = ConfirmationCode::find(array("code" => $request->get['code']));
		if ($confirmationcodes->count() > 0) {
			$code = $confirmationcodes->get(0);
			if ($code->user->status !== $code->user->_status['admin'] && $code->user->email == $request->get['verify_email']) {
				$code->user->status = $code->user->_status['live'];
				$code->user->save();
				$code->delete();
				$request->email_verification = true;
				$request->message("Your email address has now been verfified! You can login now :)");
			}
		}
	}
}

/* This checks the request to see if a user is trying to logout */
function check_logout($request) {
	if (!isset($request->get['logout']) || !isset($request->get['key']))
		return;
	if ($request->get['logout'] && $request->validate_csrf_token($request->get['key'])) {
		User::logout();
		if (isset($request->get['referrer'])) {
			header("Location: " . home_url . "/refer/?url=" . urlencode($request->get['referrer']));
		}
	}
}

/* This checks the request to see if a user is logged in */
function check_session($request) {
	if (!isset($_SESSION['user']))
		return;
	
	$user = UserSession::load_session($_SESSION['user']['userid'], $_SESSION['user']['keycode']);
	if ($user !== null) {
		$request->user = $user;
	} else {
		User::logout(); // Something is wrong with the session, best destroy it
	}
}

/* This checks the request to see if a user was logged in before this visit */
function check_cookies($request) {
	if ($request->user->logged_in())
		return;
	$user = UserSession::check_cookies();
	if ($user !== null)
		$request->user = $user;
}

/* This adds the login form to the request */
function add_login_form($request) {
	$request->login_form = new Form(array(
		new Fieldset("", array(
			"username" => new CharFormField("Username"),
			"password" => new PasswordFormField("Password"),
		))
	), $request->fullPath . "?auth_login=true", "POST");
}

/* This adds the registration form to the request */
function add_registration_form($request) {
	$request->register_form = new Form(array(
		new Fieldset("", array(
			"email" => new EmailFormField("Email", "", array("placeholder"=>"Your Email Address...")),
			"password" => new PasswordWithStrengthFormField("Password", "", array("placeholder"=>"Password...")),
			"password2" => new PasswordFormField("Password (Again)", "", array("placeholder"=>"Password... (Again)")),
		))
	), $request->fullPath . "?auth_register=true", "POST");
}

/* This checks the request to see if a user is trying to login through our form */
function check_login($request) {
	try {
		if ($request->user->logged_in() || !isset($request->get['auth_login']) || !$request->login_form->load_post_data($request->post) ||
			 !$request->login_form->get_value("username") || !$request->login_form->get_value("password")) {
			return;
		}
	} catch(Exception $e) { return; } // Most likely happens to be a get['auth_login'] with no POST data
	User::login($request, $request->login_form->get_value("username"), $request->login_form->get_value("password"));
	$request->login_form->clear_data();
}

/* This checks the request to see if a user is trying to register through our form */
function check_registration($request) {
	try {
		if (!isset($request->get['auth_register']) || !$request->register_form->load_post_data($_POST) || !$request->register_form->get_value("password") || !$request->register_form->get_value("password2")) {
			return;
		}
	} catch(Exception $e) { return; } // Most likely happens to be a get['auth_register'] with no POST data
	
	// Check passwords
	if ($request->register_form->get_value("password") != $request->register_form->get_value("password2")) {
		$request->message("Passwords didnt match!", "error");
		return;
	}
	
	// Create the user
	try {
		list($user, $code) = User::create_user($request->register_form->get_value("email"), $request->register_form->get_value("password"), $request->register_form->get_value("email"));
		$request->register_form->clear_data();
		if ($user->status >= $user->_status['live'])
			User::login($request, $request->register_form->get_value("email"), $request->register_form->get_value("password"));
		else {
			if (strlen($code) > 0)
				$request->message("Thankyou for registering! We just need you to validate your email address...", "success");
			else {
				$request->message("Thankyou for registering! An admin must validate your account before you can login...", "success");
			}
		}
	} catch(Exception $e) { $request->message($e->getMessage()); }
}

function validate_login($request) {
	if ($request->user->logged_in() && $request->user->status < $request->user->_status['live']) {
		$request->user->logout();
		if ($request->user->status == $request->user->_status['suspended'])
			$request->message("Your account has been suspended!", "error");
		if ($request->user->status == $request->user->_status['registered']) {
			if (!ConfigManager::get("disable_auth_emailer", false))
				$request->message("You must validate your email address first!", "error");
			else
				$request->message("An administrator must approve your account first!", "error");
		}
	}
}

function auth_init($request) {
	$request->user = new User();
	
	// First, see if we have a email verification code
	check_email_verification($request);

	// See if the user wants to logout
	check_logout($request);
	
	// See if the user should be logged in
	check_session($request);
	check_cookies($request);
	
	// Add the forms..
	add_login_form($request);
	add_registration_form($request);
	check_login($request);
	check_registration($request);
	
	// Lets also make sure we should be logged in!
	validate_login($request);
	
	// By now, if we should be logged in, we will be
	$request->add_val("username", $request->user->logged_in() ? $request->user->get_display_name() : "");
	$request->add_val("short_display_name", $request->user->logged_in() ? $request->user->get_short_display_name() : "");
	$request->add_val("logout_url", $request->user->logged_in() ? $request->user->get_logout_url($request) : "");
}

SignalManager::hook("page_load_setup", "auth_init");
?>

