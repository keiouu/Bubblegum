<?php
/*
 * Tikapot Models
 *
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");
require_once(home_dir . "apps/core/models.php");
require_once(home_dir . "lib/tp-git/git.php");

class Branch extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("project", new FKField("core.Project"));
		$this->add_field("name", new CharField(250));
	}
	public function __toString() { return $this->name; }
}

?>
