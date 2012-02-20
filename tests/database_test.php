<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/database.php");


class DatabaseTest extends UnitTestCase {
	function testDatabase() {
		$db = Database::create();
		$this->assertTrue($db->is_connected());
		$this->assertTrue($db->query("DROP TABLE IF EXISTS tests;"));
		$this->assertTrue($db->query("CREATE TABLE tests (test CHAR(20));"));
		$cols = $db->get_columns("tests");
		$this->assertTrue(count($cols) == 1);
		if ($db->get_type() == "psql")
			$this->assertEqual($cols, array("test" => "bpchar"));
		if ($db->get_type() == "mysql")
			$this->assertEqual($cols, array("test" => "char"));
		$this->assertTrue($db->query("INSERT INTO tests VALUES ('hello :D');"));
		$this->assertTrue($db->query("SELECT * FROM tests;"));
		$this->assertTrue($db->query("DROP TABLE IF EXISTS tests;"));
	}
}

?>

