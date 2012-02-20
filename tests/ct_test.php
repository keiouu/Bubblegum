<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "contrib/auth/models.php");
require_once(home_dir . "framework/model_query.php");
require_once(home_dir . "framework/database.php");
require_once(home_dir . "framework/models.php");

class CTTestM extends Model {}

class CTTest extends UnitTestCase {
	function testCT() {
		$obj = new ContentType();
		$ct = ContentType::of($obj);
		$obj2 = new CTTestM();
		$ct2 = ContentType::of($obj2);
		$this->assertNotEqual($ct->pk, $ct2->pk);
		$ct = ContentType::of($obj2);
		$object = $ct->obtain();
		$this->assertTrue($object);
	}
}

?>

