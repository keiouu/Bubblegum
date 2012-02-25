<?php
/*
 * Admin - Register framework models
 *
 */

require_once(home_dir . "contrib/admin/core.php");
require_once(home_dir . "framework/models.php");

AdminModel::register("core", new Config());
AdminModel::register("core", new App_Config());
?>

