<?php
/*
 * Tikapot Auth Models
 *
 */

require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "framework/session.php");
require_once(home_dir . "framework/utils.php");
require_once(home_dir . "framework/signal_manager.php");
require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "framework/cookie_manager.php");

class ConfirmationCode extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("user", new FKField("auth.User"));
		$this->add_field("code", new CharField($max_length=10));
	}
	
	public function __toString() {
		return $this->code;
	}
	
	public function email() {
		$to = $this->user->email;
		$from = ConfigManager::get('email_address', "no-reply@example.com");
		$subject = "Email Verification";
		$body = '
			Welcome to '.project_name.'!
			We just need a second to verify your email address is correct.
			If you did not register an account at '.site_url.' please do NOT visit the link below!
			If you did want an account, please copy the following link and paste it into your browser:)
			'.site_url.home_url.'?verify_email='.$to.'&code='.$this->code.'
			Thankyou!
		';
		$headers = 'From: ' . $to . "\r\n";
		return mail($to, $subject, $body, $headers);
	}
	
	public static function genCode($user) {
		$loc = rand(1, 25);
		$str = sha1($loc.microtime());
		$code = new ConfirmationCode();
		$code->user = $user->pk;
		$code->code = substr($str, $loc, 10);
		$code->save();
		return $code;
	}
}

class UserSession extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("user", new FKField("auth.User"));
		$this->add_field("keycode", new CharField($max_length=40));
		$this->add_field("expires", new DateTimeField());
	}
	
	public static function check_session($userid, $keycode) {
		$arr = UserSession::find(array("user"=>$userid));
		if (count($arr) <= 0)
			return false;
		$session = $arr->get(0);
		return $session->keycode == $keycode;
	}
	
	public static function load_session($userid, $keycode) {
		if (!UserSession::check_session($userid, $keycode))
			return null;
		$user = User::get($userid);
		if (isset($_SESSION['user']) && isset($_SESSION['user']['userid'])) {
			$user->update_session();
		} else {
			$user->construct_session(true);
		}
		return $user;
	}
	
	public static function check_cookies() {
		$pk = CookieManager::get("tp_auth_id");
		$kc = CookieManager::get("tp_auth_kc");
		if ($pk !== null && $kc !== null) {
			$user = UserSession::load_session($pk, $kc);
			if ($user === null) {
				CookieManager::delete("tp_auth_id");
				CookieManager::delete("tp_auth_kc");
			}
			return $user;
		}
		return null;
	}
	
	public function save() {
		parent::save();
		CookieManager::set("tp_auth_id", $this->user->pk, ConfigManager::get('session_timeout', -1));
		CookieManager::set("tp_auth_kc", $this->keycode, ConfigManager::get('session_timeout', -1));
	}
	
	public function delete() {
		CookieManager::delete("tp_auth_id");
		CookieManager::delete("tp_auth_kc");
		parent::delete();
	}
}

class Permission extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("name", new CharField($max_length=128));
	}
	
	public function __toString() {
		return $this->name;
	}
}

class User_Permission extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("user", new FKField("auth.User"));
		$this->add_field("permission", new FKField("auth.Permission"));
		$this->add_field("value", new BooleanField(true));
	}
	
	public function __toString() {
		return $this->permission->name;
	}
}

class AuthException extends Exception {}

class User extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("username", new CharField($max_length=40));
		$this->add_field("password", new CharField($max_length=128));
		$this->add_field("email", new CharField($max_length=50));
		$this->add_field("status", new ChoiceField(array(
			"-1" => "suspended",
			"0" => "registered",
			"1" => "live",
			"2" => "moderator",
			"3" => "admin",
		), "0"));
		$this->add_field("created", new DateTimeField($auto_now_add = True));
		$this->add_field("last_login", new DateTimeField($auto_now_add = True, $auto_now = True));
		$this->_version = "1.2";
	}
	
	public function upgrade($db, $old_version, $new_version) {
		if ($old_version == "1.0") {
			$db->query('ALTER TABLE "user" ALTER "password" TYPE character varying(128);');
		}
		if ($old_version <= 1.1) {
			$db->query('UPDATE "user" SET "status"=\'-1\' WHERE "status"=\'2\';');
		}
		return parent::upgrade($db, $old_version, $new_version);
	}
	
	public function get_logout_url($request) {
		$key = $this->get_new_session_key();
		$_SESSION['auth']['logout_key'] = $key;
		return $request->fullPath . '?logout=true&key=' . $key;
	}
	
	public function get_short_display_name() {
		if (strlen($this->email) == 0)
			return "Anonymous";
		return prettify(substr($this->email, 0, strpos($this->email, "@")));
	}
	
	public function get_display_name() {
		if (strlen($this->email) == 0)
			return "Anonymous";
		return $this->email;
	}
	
	public function __toString() {
		return $this->get_display_name();
	}
	
	public function get_admin_link() {
		return home_url . "admin/auth/User/edit/".$this->pk."/";
	}
	
	public function is_admin() {
		return $this->status == $this->_status['admin'];
	}
	
	public function logged_in() {
		return isset($_SESSION['user']) && ($_SESSION['user']['userid'] == $this->pk);
	}
	
	public function has_permission($name, $default = false) {
		if ($this->status == $this->_status['admin'])
			return true;
		
		list($permission, $created) = Permission::get_or_create(array("name" => strtolower($name)));
		if ($created)
			return $default;
		
		$perm = User_Permission::get_or_ignore(array("user" => $this->pk, "permission" => $permission->pk));
		return $perm === null ? $default : $perm->value;
	}
	
	public function set_permission($name, $value = true) {
		list($permission, $created) = Permission::get_or_create(array("name" => strtolower($name)));
		list($perm, $created) = User_Permission::get_or_create(array("user" => $this->pk, "permission" => $permission->pk));
		$perm->value = $value;
		$perm->save();
		return $value;
	}
	
	public function give_permission($name) {
		return $this->set_permission($name, true);
	}
	
	public function revoke_permission($name) {
		return $this->set_permission($name, false);
	}
	
	public static function logout() {
		if (isset($_SESSION['user']) && isset($_SESSION['user']['userid'])) {
			$session = UserSession::get_or_ignore(array("user" => $_SESSION['user']['userid']));
			if ($session)
				$session->delete();
		}
		Session::delete("user");
		CookieManager::delete("tp_auth_id");
		CookieManager::delete("tp_auth_kc");
	}
	
	public function update_session($usersession = Null) {
		if ($usersession === Null)
			$usersession = UserSession::get(array("user" => $this->pk));
		$expiry = time() + ConfigManager::get('session_timeout', 0);
		$usersession->expires = date(DateTimeField::$FORMAT, $expiry);
		$usersession->save();
	}
	
	private function get_new_session_key() {
		return sha1($this->pk + (microtime() * rand(0, 198)));
	}
	
	public function construct_session($new_session = False) {
		list($usersession, $created) = UserSession::get_or_create(array("user"=>$this->pk));
		if ($created || $new_session) {
			$usersession->keycode = $this->get_new_session_key();
			$_SESSION['user'] = array("userid" => $this->pk, "keycode" => $usersession->keycode);
		} else {
			if ($usersession->expires < date(DateTimeField::$FORMAT, time())) {
				$this->logout($usersession);
				throw new AuthException("Your session has timed out");
				return;
			}
			if ($usersession->keycode != $_SESSION['user']['keycode']) {
				$this->logout();
				throw new AuthException("Error: session key does not match!");
				return;
			}
		}
		$this->update_session($usersession);
	}
	
	public static function encode_old($password) {
		$salted = ConfigManager::get('password_salt', "") . $password;
		return sha1($salted);
	}
	
	public static function encode($password) {
		$salted = ConfigManager::get('password_salt', "") . $password . ConfigManager::get('password_salt2', "");
		return hash("sha512", $salted);
	}
	
	public static function auth_encoded($request, $username, $password, $new_session=False) {
		$arr = User::find(array("username" => $username, "password" => $password));
		if (count($arr) <= 0) {
			SignalManager::fire("auth_login_fail", array($request, $username));
			throw new AuthException("Username/Password incorrect!");
		}
		$user = $arr->get(0);
		$user->construct_session($new_session);
		$user->save(); // Update last_login
		SignalManager::fire("auth_login", array($request, $user));
		return $user;
	}
	
	public static function login($request, $username, $password) {
		try {
			$request->user = User::auth_encoded($request, $username, User::encode($password), True);
			$request->message("You are now logged in!", "success");
		} catch (AuthException $e) {
			try {
				$request->user = User::auth_encoded($request, $username, User::encode_old($password), True);
				try {
					$request->user->password = User::encode($password);
					$request->user->save();
				} catch (Exception $e) {
					$request->message("Looks like you need to upgrade your database!", "error");
				}
			} catch (AuthException $e) {
				$request->message($e->getMessage(), "error");
			}
		}
		return $request->user->logged_in();
	}
	
	/* Shortcut */
	public static function create_user($username, $password, $email, $status = "0", $prevent_code_email = false) {
		$password = User::encode($password);
		if(User::find(array("username"=>$username))->count() > 0)
			throw new AuthException("Error: Username exists!");
		
		SignalManager::fire("auth_pre_create", array($username, $password, $email, $status));		
		$user = User::create(array("username" => $username, "password" => $password, "email" => $email, "status" => $status));
		SignalManager::fire("auth_post_create", array($user));
		
		if (ConfigManager::get("auth_dont_verify", false)) {
			$user->status = $user->_status['live'];
			$user->save();
		} else {
			if (!$prevent_code_email && !ConfigManager::get("disable_auth_emailer", true)) {
				$code = ConfirmationCode::genCode($user);
				$code->email();
				return array($user, $code);
			}
		}
		return array($user, "");
	}
	
	/* Shortcut */
	public static function delete_user($username) {
		try {
			$user = User::get(array("username"=>$username));
			$user->delete();
		}
		catch (Exception $e) {
			return false;
		}
		return true;
	}
}

?>

