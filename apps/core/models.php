<?php
/*
 * Tikapot Models
 *
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "framework/utils.php");
require_once(home_dir . "contrib/auth/models.php");
require_once(home_dir . "lib/tp-git/git.php");

function get_potential_assignees() {
	$assignees = array();
	
	// Do Teams, and their members
	foreach (Team::objects() as $team) {
		$assignees[$team->pk] = $team->name;//array();
		//foreach (Team_Link::find(array("team" => $team->pk)) as $team_link) {
		//	$assignees[$team->name][] = $team_link->user;
		//}
	}
	
	// All users
	$assignees["All Users"] = array();
	foreach (User::objects() as $user) {
		$assignees["All Users"][] = $user;
	}
	
	return $assignees;
}

class Log extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("content", new TextField());
	}
	public function __toString() { return $this->content; }
}

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
	
	public function __toString() {
		return $this->name;
	}
	
	public function post_save($pk) { // We use save in case the leader changes
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
		$this->add_field("owner", new MultiFKField("auth.User", "core.Team", "core.Organisation"));
		$this->add_field("public", new BooleanField(true));
		$this->add_field("created", new DateTimeField(true));
		$this->add_field("updated", new DateTimeField(true, true));
	}
	
	public function __toString() { 
		return $this->name;
	}
	
	public function post_save($pk) { // We use save in case the owner changes
		Project_Link::get_or_create(array("project" => $this->pk, "user" => $this->owner->pk));
	}
	
	public function gitDir() {
		return repo_dir . $this->pk;
	}
	
	public function post_create() {
		// Create git repository
		$this->getRepository();
	}
	
	public function getRepository() {
		$path = $this->gitDir();
		if (!file_exists($path))
			Git::Init($path, $this->description);
		if (file_exists($path)) {
			return new Git($path);
		}
		return null;
	}
	
	public static function mine($user) {
		// All projects a user is a member of
		$projects = array();
		foreach (Project::find(array("owner" => $user)) as $project) {
			$projects[$project->pk] = $project;
		}
		foreach (Project_Link::find(array("user" => $user)) as $plink) {
			$projects[$plink->project->pk] = $plink->project;
		}
		if (count($projects) <= 0)
			return null;
		$ids = "";
		foreach($projects as $id => $project) {
			if (strlen($ids) > 0)
				$ids .= ",";
			$ids .= $id;
		}
		return Project::find(array("pk" => array("(" . $ids . ")", "IN")));
	}
	
	public function track($user) {
		Project_Link::get_or_create(array(
			"project" => $this->pk,
			"user" => $user->pk
		));
	}
	
	public function untrack($user) {
		$link = Project_Link::get_or_ignore(array(
			"project" => $this->pk,
			"user" => $user->pk
		));
		if ($link)
			$link->delete();
	}
	
	public function tracked_by($user) {
		return Project_Link::get_or_ignore(array(
			"project" => $this->pk,
			"user" => $user->pk
		)) === null;
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
			"5" => "QA",
			"s" => "Security",
			"a" => "Administration",
			"4" => "n/a",
		), "1"));
		$this->add_field("priority", new ChoiceField(array(
			"0" => "Critical",
			"1" => "High",
			"2" => "Medium",
			"3" => "Low",
			"4" => "n/a",
		), "2"));
		$this->add_field("status", new ChoiceField(array(
			"0" => "New",
			"1" => "Confirmed",
			"2" => "In Progress",
			"3" => "Testing",
			"4" => "Awaiting Feedback",
			"5" => "Complete",
			"6" => "n/a",
		), "0"));
		$this->add_field("project", new FKField("core.Project"));
		$this->add_field("milestone", new FKField("core.Milestone", false));
		$this->add_field("progress", new IntegerField(6, 0));
		$this->add_field("created", new DateTimeField(true));
		$this->add_field("created_by", new FKField("auth.User"));
		$this->add_field("due", new DateTimeField(false));
		$this->add_field("updated", new DateTimeField(true, true));
		$this->add_field("completed", new DateTimeField(false));
		$this->add_field("completed_by", new FKField("auth.User", false));
	}
	public function __toString() { return $this->name; }
	
	public function assigned($obj) {
		$link = Task_Link::get_or_ignore(array("task" => $this->pk, "assignee" => $obj));
		if ($link)
			return true;
		
		// Check Teams
		if (get_class($obj) == "User") {
			$team_links = Team_Link::find(array("user" => $obj));
			foreach ($team_links as $team_link) {
				if ($this->assigned($team_link->team))
					return true;
			}
		}
		
		return false;
	}
	
	public function assignees($full = false) {
		$string = "";
		$links = Task_Link::find(array("task" => $this->pk));
		foreach ($links as $link) {
			if (strlen($string) > 0)
				$string .= ", ";
			$string .= ($full ? $link->_assignee->__toString() : $link->assignee);
		}
		return $string;
	}
}

class Task_Link extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("task", new FKField("core.Task"));
		$this->add_field("assignee", new MultiFKField("auth.User", "core.Team", "core.Organisation"));
		$this->add_field("assigned", new DateTimeField(true));
	}
}

?>

