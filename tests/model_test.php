<?php
/*
 * Tikapot
 *
 */

require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_query.php");
require_once(home_dir . "framework/database.php");

class TestModel extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("test_prop", new CharField($max_length=7));
		$this->add_field("other_prop", new NumericField(4.5));
	}
}

class TestModel2 extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("test_pk", new PKField(7, 0, True));
	}
}

class ModelTest extends UnitTestCase {
	function testModelTableValidation() {
		$obj = new TestModel();
		$this->assertTrue($obj->verify_table());
	}
	
	function testModelDB() {
		$db = Database::create();
		$obj = new TestModel();
		if ($db->get_type() == "mysql") {
			$this->assertEqual($obj->db_create_query($db), "CREATE TABLE \"testmodel\" (\"id\" BIGINT (22) AUTO_INCREMENT PRIMARY KEY, \"test_prop\" VARCHAR (7), \"other_prop\" NUMERIC DEFAULT '4.5');");
		}
		if ($db->get_type() == "psql") {
			$this->assertEqual($obj->db_create_query($db), "CREATE TABLE \"testmodel\" (\"id\" BIGINT DEFAULT nextval('testmodel_id_seq'), \"test_prop\" VARCHAR (7), \"other_prop\" NUMERIC DEFAULT '4.5', CONSTRAINT testmodel_pkey PRIMARY KEY (id));");
			$this->assertEqual($obj->db_create_extra_queries_pre($db, "testmodel"), array("CREATE SEQUENCE testmodel_id_seq;"));
		}
		if ($db->get_type() == "mysql")
			$this->assertEqual($obj->insert_query($db), "INSERT INTO \"testmodel\" (\"test_prop\", \"other_prop\") VALUES ('', 4.5);");
		if ($db->get_type() == "psql")
			$this->assertEqual($obj->insert_query($db), "INSERT INTO \"testmodel\" (\"test_prop\", \"other_prop\") VALUES ('', 4.5) RETURNING \"id\";");
		$this->assertTrue($obj->create_table());
		$this->assertTrue($obj->save());
		
		$test_field = new CharField($max_length=7, "test");
		$this->assertEqual($test_field->db_create_query($db, "test_field", "testmodel"), "\"test_field\" VARCHAR (7) DEFAULT 'test'");
		$test_field = new CharField($max_length=2);
		$this->assertEqual($test_field->db_create_query($db, "test_field", "testmodel"), "\"test_field\" VARCHAR (2)");
		$test_field = new CharField();
		$this->assertEqual($test_field->db_create_query($db, "test_field", "testmodel"), "\"test_field\" VARCHAR");
		$test_field = new NumericField(1.0, "4,2");
		$this->assertEqual($test_field->db_create_query($db, "test_field", "testmodel"), "\"test_field\" NUMERIC (4,2) DEFAULT '1'");
		$test_field = new NumericField(1.0);
		$this->assertEqual($test_field->db_create_query($db, "test_field", "testmodel"), "\"test_field\" NUMERIC DEFAULT '1'");
		$test_field = new NumericField();
		$this->assertEqual($test_field->db_create_query($db, "test_field", "testmodel"), "\"test_field\" NUMERIC");
		$test_field = new IntegerField();
		$this->assertEqual($test_field->db_create_query($db, "test_field", "testmodel"), "\"test_field\" INT DEFAULT '0'");
		$test_field = new BigIntField();
		$this->assertEqual($test_field->db_create_query($db, "test_field", "testmodel"), "\"test_field\" BIGINT DEFAULT '0'");
	}
	
	function testModels() {
		$obj = new TestModel();

		// Test Defaults
		$this->assertEqual($obj->other_prop, 4.5);

		// Test setting (and getting)
		$obj->test_prop = '5';
		$this->assertEqual($obj->test_prop, '5');
		$obj->test_prop = '3';
		$this->assertEqual($obj->test_prop, '3');
		
		// Test setting an invalid field name
		$failed = False;
		try { $obj->not_a_valid_field = 100; } catch(Exception $e) { $failed = True; }
		$this->assertTrue($failed);
		
		// Test unsetting
		unset($obj->test_prop);
		$fields = $obj->get_fields();
		$this->assertEqual($obj->test_prop, $fields['test_prop']->get_default());
	}
	
	function testCustomPKs() {
		$obj = new TestModel2();
		$this->assertEqual($obj->pk, $obj->test_pk);
		$this->assertEqual($obj->get_field("pk"), $obj->get_field("test_pk"));
		$this->assertTrue(isset($obj->pk));
		$this->assertFalse(isset($obj->id));
	}
	function testModelValidation() {
		// Test validation
		$obj = new TestModel();
		$obj->test_prop = '123456789';
		$this->assertFalse($obj->validate());
		$this->assertTrue(count($obj->get_errors()) > 0);
		$obj->test_prop = '1234567';
		$this->assertTrue($obj->validate());
		$this->assertTrue(count($obj->get_errors()) == 0);
		$obj->test_prop = '123456';
		$this->assertTrue($obj->validate());
	}
}

?>

