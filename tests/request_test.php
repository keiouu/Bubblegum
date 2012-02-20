<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/request.php");


class RequestTest extends UnitTestCase {
	function testRequest() {
		$req = new Request();
		$this->assertEqual($req->get_mime_type("/notafile/"), "text/html");
		$this->assertEqual($req->get_mime_type(home_dir . "tests/randoms/test_mime.txt"), "text/plain");
		$this->assertEqual($req->get_mime_type(home_dir . "tests/randoms/test_mime.css"), "text/css");
	}
}

?>

