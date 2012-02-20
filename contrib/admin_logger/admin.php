<?php
/*
 * Admin
 *
 */

require_once(home_dir . "contrib/admin/core.php");
require_once(dirname(__FILE__) . "/models.php");

AdminModel::register("logs", new Admin_log());
?>

