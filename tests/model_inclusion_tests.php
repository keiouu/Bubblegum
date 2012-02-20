<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "framework/database.php");
require_once(home_dir . "tests/testModels/models.php");


class ModelInclusionFieldTest extends UnitTestCase {
	function testBasicInclusion() {
		$obj = new TestInclusionModelTwo();
		$this->assertTrue($obj->val_prop === "hey");
		$obj->val_prop = "no";
		$this->assertTrue($obj->val_prop === "no");
		$obj->save();
		$obj2 = TestInclusionModelTwo::get($obj->pk);
		$this->assertTrue($obj2->val_prop === "no");
	}
	
	function testMultiInclusion() {
		$obj = new TestInclusionModelThree();
		$this->assertTrue($obj->val_prop === "bye");
		$obj->val_prop = "no";
		$this->assertTrue($obj->val_prop === "no");
		$this->assertTrue($obj->inc->val_prop === "no");
		$obj->save();
		$this->assertTrue($obj->val_prop === "no");
		$this->assertTrue($obj->inc->val_prop === "no");
	}
	
	function testUniInclusion() {
		$obj = new TestInclusionModelFour();
		$this->assertTrue($obj->val_prop === "bye");
		$obj->val_prop = "no";
		$this->assertTrue($obj->val_prop === "no");
		$this->assertTrue($obj->inc->val_prop === "hey");
		$obj->save();
		$this->assertTrue($obj->val_prop === "no");
		$this->assertTrue($obj->inc->val_prop === "hey");
	}
}
?>

