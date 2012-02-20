<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "framework/database.php");

class TestModelFK extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("val_prop", new CharField("", $max_length=7));
		$this->add_field("other_prop", new FKField("testModels.TestFKModel"));
	}
}

class TestModelM2M extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("tests", new M2MField("testModels.TestFKModel"));
	}
}

class ModelFieldTest extends UnitTestCase {
	function testFKField() {
		$field = new FKField("testModels.TestFKModel");
		$field->test_prop = "hello";
		$field->save();
		$this->assertTrue($field->validate());
		$model = new TestModelFK();
		$model->val_prop = "testme";
		$obj = $model->other_prop;
		$obj->test_prop = "testtoo";
		$this->assertEqual($obj->test_prop, "testtoo");
		$id = $model->save();
		$test = TestModelFK::get($id);
		$this->assertTrue($test);
		$this->assertEqual($obj->test_prop, $obj->test_prop);
		$field = new FKField("framework.ContentType");
		$this->assertTrue($field->validate());
	}
	
	function testM2MField() {
		//$model = new TestModelM2M();
		//$obj = $model->tests->get_or_create(array("test_prop" => "why"));
		//$this->assertTrue($obj);
		//$this->assertTrue($model->tests->count() > 0);
		//$obj = $model->tests->find(array("test_prop" => "why"))->get();
		//$this->assertEqual($obj->test_prop, "why");
		//$obj = $model->tests->get_or_create(array("test_prop" => "whyheythere"));
	}
	
	function testCharField() {
		$field = new CharField(5, "a");
		$this->assertEqual($field->get_value(), "a");
		$field->set_value("abcde");
		$this->assertTrue($field->validate());
		$field->set_value("abcdef");
		$this->assertFalse($field->validate());
		$field->set_value("abc'd");
		$this->assertTrue($field->validate());
		$db = Database::create();
		$this->assertEqual($field->sql_value($db), "'abc''d'");
	}
	
	function testTextField() {
		$field = new TextField("aaa");
		$this->assertEqual($field->get_value(), "aaa");
		$field->set_value("abcdeabcdeabcdeabcdeabcdeabcdeabcde");
		$this->assertNotEqual($field->get_value(), "aaa");
		$this->assertTrue($field->validate());
		$db = Database::create();
		$this->assertEqual($field->db_create_query($db, "test", "t1"), "\"test\" TEXT DEFAULT 'aaa'");
	}
	
	function testIntegerField() {
		$field = new IntegerField(9, 1, False, "");
		$this->assertEqual($field->get_value(), 1);
		$field->set_value(123456789);
		$this->assertTrue($field->validate());
		$field->set_value(1234567891);
		$this->assertFalse($field->validate());
		$field->set_value(12345.34);
		$this->assertFalse($field->validate());
		$field->set_value("NotanInt");
		$this->assertFalse($field->validate());
		$field->set_value("");
		$this->assertTrue($field->validate());
	}
	
	function testBoolField() {
		$field = new BooleanField(False);
		$this->assertTrue($field->validate());
		$this->assertFalse($field->get_value());
		$field->set_value(True);
		$this->assertTrue($field->get_value());
		$this->assertTrue($field->validate());
		$field->set_value("3");
		$this->assertFalse($field->validate());
	}
	
	function testDateField() {
		$field = new DateField();
		$this->assertTrue($field->validate());
		$field->set_value("1999-01-21");
		$this->assertTrue($field->validate());
		$field->set_value("dfg1");
		$this->assertFalse($field->validate());
		$field->set_value("abcd-ef-gh");
		$this->assertFalse($field->validate());
		$field->set_value(date("Y-m-d"));
		$this->assertTrue($field->validate());
		
		$field = new DateField($auto_now_add = True, $auto_now = True);
		$field->pre_save(Null, True);
		$this->assertEqual($field->get_value(), date(DateField::$FORMAT, time()));
		$field = new DateField($auto_now_add = True, $auto_now = False, date(DateField::$FORMAT, strtotime("1990-01-12 12:12:12")));
		$field->pre_save(Null, True);
		$this->assertNotEqual($field->get_value(), date(DateField::$FORMAT, time()));
		$field->pre_save(Null, False);
		$this->assertEqual($field->get_value(), date(DateField::$FORMAT, time()));
	}
	
	function testDateTimeField() {
		$field = new DateTimeField();
		$this->assertTrue($field->validate());
		$field->set_value("1999-01-21 24:54:21");
		$this->assertTrue($field->validate());
		$field->set_value("1999-01-21 24/54/21");
		$this->assertFalse($field->validate());
		$field->set_value("199-01-21 12:3:2");
		$this->assertFalse($field->validate());
		$field->set_value("abcd-ef-gh ad:fe:gt");
		$this->assertFalse($field->validate());
		$field->set_value(date("Y-m-d h:m:s"));
		$this->assertTrue($field->validate());
		
		$field = new DateTimeField($auto_now_add = True, $auto_now = True);
		$field->pre_save(Null, True);
		$this->assertEqual($field->get_value(), date(DateTimeField::$FORMAT, time()));
		$field = new DateTimeField($auto_now_add = True, $auto_now = False, date(DateField::$FORMAT, strtotime("1990-01-12 12:12:12")));
		$field->pre_save(Null, True);
		$this->assertNotEqual($field->get_value(), date(DateTimeField::$FORMAT, time()));
		$field->pre_save(Null, False);
		$this->assertEqual($field->get_value(), date(DateTimeField::$FORMAT, time()));
	}
}
?>

