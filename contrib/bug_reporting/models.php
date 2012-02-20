<?php
/*
 * Tikapot Bug Reporting Models
 *
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");

class Bug extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("content", new TextField());
		$this->add_field("status", new ChoiceField(array(
			"1" => "Reported",
			"2" => "Confirmed",
			"3" => "In Progress",
			"4" => "Testing",
			"5" => "Fixed",
		), "1"));
		$this->add_field("priority", new ChoiceField(array(
			"0" => "Critical",
			"1" => "High",
			"2" => "Medium",
			"3" => "Low",
			"4" => "Not Important",
		), "2"));
		$this->add_field("reported_by", new FKField("auth.User"));
		$this->add_field("reported_on", new DateTimeField(true));
		$this->add_field("completed", new BooleanField(false));
		$this->add_field("completed_by", new FKField("auth.User"));
		$this->add_field("completed_on", new DateTimeField(false));
	}
}

?>

