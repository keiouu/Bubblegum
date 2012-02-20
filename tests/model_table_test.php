<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_query.php");
require_once(home_dir . "framework/database.php");

class TestTableModel extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("test_prop", new CharField("", $max_length=7));
	}
	
	/* This is designed to test table validation, a table should exist by this name from the last test with different fields */
	public function get_table_name() {
		return "testmodel";
	}
}

class ModelTableTest extends UnitTestCase {
	function testModelTableValidation() {
		$obj = new TestTableModel();
		$this->expectException();
		$this->assertFalse($obj->verify_table());
	}
}

?>

