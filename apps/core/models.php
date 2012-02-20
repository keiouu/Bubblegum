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
	public function __toString() { return $this->name; }
}

class Team extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("name", new CharField(250));
		$this->add_field("description", new TextField());
		$this->add_field("leader", new FKField("auth.User"));
	}
	public function __toString() { return $this->name; }
	
	public function post_save($pk) {
		Team_Link::get_or_create(array("team" => $this->pk, "user" => $this->leader->pk));
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
	public function __toString() { return $this->name; }
	
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
		$this->add_field("project", new FKField("core.Project"));
		$this->add_field("name", new CharField(250));
		$this->add_field("description", new TextField());
		$this->add_field("created", new DateTimeField(true));
		$this->add_field("updated", new DateTimeField(true, true));
	}
	public function __toString() { return $this->name; }
}

class Task extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("name", new CharField(250));
		$this->add_field("description", new TextField());
		$this->add_field("type", new ChoiceField(array(
			"0" => "Bug",
			"1" => "Feature",
			"2" => "Enhancement",
			"3" => "Aesthetic",
			"4" => "n/a",
		), "1"));
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
	public function __toString() { return $this->name; }
	
	public function assigned($obj) {
		$string = get_class($obj) . "|" . $obj->pk;
		$link = Task_Link::get_or_ignore(array("task" => $this->pk, "assignee" => $string));
		if ($link)
			return true;
		
		// Check Teams
		if (get_class($obj) == "User") {
			$team_links = Team_Link::find(array("user" => $obj->pk));
			foreach ($team_links as $team_link) {
				if ($this->assigned($team_link->team))
					return true;
			}
		}
		
		return false;
	}
	
	public function assignees() {
		$string = "";
		$links = Task_Link::find(array("task" => $this->pk));
		foreach ($links as $link) {
			if (strlen($string) > 0)
				$string .= ", ";
			$string .= $link->assignee;
		}
		return $string;
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

