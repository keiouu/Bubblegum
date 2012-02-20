<?php
/*
 * Admin
 *
 */

require_once(home_dir . "contrib/admin/core.php");
require_once(dirname(__FILE__) . "/models.php");

AdminModel::register("Users", new Organisation());
AdminModel::register("Users", new Team());
AdminModel::register("Users", new Team_Link());

AdminModel::register("Projects", new Project());
AdminModel::register("Projects", new Project_Link());
AdminModel::register("Projects", new Milestone());
AdminModel::register("Projects", new Task());
AdminModel::register("Projects", new Task_Link());
?>

