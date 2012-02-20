<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "contrib/auth/models.php");
require_once(home_dir . "framework/request.php");


class AuthTest extends UnitTestCase {
	function testAuth() {
		$old_session = $_SESSION;
		$username = "testMan";
		$password = "aTestMansPassword";
		User::delete_user($username);
		list($user, $code) = User::create_user($username, $password, "test@tikapot.com");
		$this->assertTrue(User::auth_encoded(new Request(), $username, User::encode($password)));
		try {
			User::auth_encoded(new Request(), $username, "wrongpassword");
			$this->assertTrue(False);
		} catch (AuthException $e) {
			$this->assertTrue(True);
		}
		$session = UserSession::get(array("user" => $user->id));
		$session->delete();
		$user->delete();
		$_SESSION = $old_session;
	}
}

?>

