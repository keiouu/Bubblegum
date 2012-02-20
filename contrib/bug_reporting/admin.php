<?php
/*
 * Admin
 *
 */

require_once(home_dir . "contrib/admin/core.php");
require_once(dirname(__FILE__) . "/forms.php");
require_once(dirname(__FILE__) . "/models.php");

AdminModel::register("bugs", new Bug(), null, array("content"));
?>

