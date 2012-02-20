<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/session.php");


class SessionTest extends UnitTestCase {
	function testSession() {
		$old_session = $_SESSION;
		Session::delete("Test");
		$this->assertEqual(Session::get("Test"), NULL);
		
		$new = Session::store("Test", 2);
		$this->assertEqual(Session::get("Test"), 2);
		$this->assertEqual($new, Session::get("Test"));
		
		$old = Session::store("Test", 5);
		$this->assertEqual(Session::get("Test"), 5);
		$this->assertEqual($old, $new);
		$no = Session::put("Test", 6);
		$this->assertEqual($no, False);
		$this->assertEqual(Session::get("Test"), 5);
		$this->assertTrue(Session::get("b43542y2") == NULL);
		Session::delete("Test");
		$this->assertEqual(Session::get("Test"), NULL);
		$_SESSION = $old_session;
	}
}

?>

