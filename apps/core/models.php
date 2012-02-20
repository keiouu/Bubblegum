<?php
/*
 * Tikapot Models
 *
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");

class Organisation extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("name", new CharField(250));
		$this->add_field("description", new TextField());
	}
}

class Team extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("name", new CharField(250));
		$this->add_field("description", new TextField());
		$this->add_field("leader", new FKField("auth.User"));
	}
}

class Team_Link extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("team", new FKField("core.Team"));
		$this->add_field("user", new FKField("auth.User"));
		$this->add_field("joined", new DateTimeField(true));
	}
}

class Project extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("name", new CharField(250));
		$this->add_field("description", new TextField());
		$this->add_field("owner", new CharField(250));
		$this->add_field("created", new DateTimeField(true));
		$this->add_field("updated", new DateTimeField(true, true));
	}
	
	public function __set_owner($obj) {
		return get_class($obj) . "|" . $obj->pk;
	}
	
	public function __get_owner($field_value) {
		list($type, $pk) = explode("|", $field_value, 2);
		return $type::get_or_ignore(array("pk" => $pk));
	}
}

class Project_Link extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("project", new FKField("core.Project"));
		$this->add_field("user", new FKField("auth.User"));
		$this->add_field("assigned", new DateTimeField(true));
	}
}

class Milestone extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("name", new CharField(250));
		$this->add_field("description", new TextField());
		$this->add_field("project", new FKField("core.Project"));
		$this->add_field("created", new DateTimeField(true));
		$this->add_field("updated", new DateTimeField(true, true));
	}
}

class Task extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("name", new CharField(250));
		$this->add_field("description", new TextField());
		$this->add_field("priority", new ChoiceField(array(
			"0" => "Critical",
			"1" => "High",
			"2" => "Medium",
			"3" => "Low",
			"4" => "n/a",
		), "2"));
		$this->add_field("project", new FKField("core.Project"));
		$this->add_field("milestone", new FKField("core.Milestone"));
		$this->add_field("progress", new IntegerField(6, 0));
		$this->add_field("created", new DateTimeField(true));
		$this->add_field("due", new DateTimeField(false));
		$this->add_field("updated", new DateTimeField(true, true));
		$this->add_field("completed", new DateTimeField(false));
	}
}

class Task_Link extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("task", new FKField("core.Task"));
		$this->add_field("assignee", new CharField(250));
		$this->add_field("assigned", new DateTimeField(true));
	}
	
	public function __set_assignee($obj) {
		return get_class($obj) . "|" . $obj->pk;
	}
	
	public function __get_assignee($field_value) {
		list($type, $pk) = explode("|", $field_value, 2);
		return $type::get_or_ignore(array("pk" => $pk));
	}
}

?>

