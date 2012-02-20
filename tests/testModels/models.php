<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "framework/model.php");
require_once(home_dir . "framework/model_fields/init.php");

class TestFKModel extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("test_prop", new CharField("", $max_length=7));
	}
}

class TestInclusionModelOne extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("val_prop", new CharField(7, "hey"));
	}
}

class TestInclusionModelTwo extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_inclusive_field("inc", new FKField("testModels.TestInclusionModelOne"));
	}
}

class TestInclusionModelThree extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_inclusive_field("inc", new FKField("testModels.TestInclusionModelOne"));
		$this->add_field("val_prop", new CharField(7, "bye"));
	}
}

class TestInclusionModelFour extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_unidirectional_inclusive_field("inc", new FKField("testModels.TestInclusionModelOne"));
		$this->add_field("val_prop", new CharField(7, "bye"));
	}
}
?>

