<?php
/*
 * Tikapot Models
 *
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "apps/core/models.php");
require_once(home_dir . "lib/tp-git/git.php");

class Deployment extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("creator", new FKField("auth.User"), true);
		$this->add_field("project", new FKField("core.Project"), true);
		$this->add_field("server_url", new CharField(350, "you@yourserver.com:path/to/git"));
		$this->add_field("key_file", new CharField(350), true);
	}
	
	public function __toString() { return $this->server_url; }
	
	public function pre_create() {
		// Generate an SSH key for this deployment
		$git = $this->project->getRepository();
		$file = $git->genKey();
		$this->key_file = $file;
		
		return parent::pre_create();
	}
	
	public function get_key() {
		return file_get_contents($this->key_file);
	}
	
	public static function create($user, $project, $url) {
		list($deployment, $created) = Deployment::get_or_create(array(
			"creator" => $user,
			"project" => $project,
			"server_url" => $url
		));
		
		return $deployment->get_key();
	}
}

?>
