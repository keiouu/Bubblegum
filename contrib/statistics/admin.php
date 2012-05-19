<?php
/**
 * Admin
 *
 */

require_once(home_dir . "contrib/admin/core.php");
require_once(dirname(__FILE__) . "/models.php");

AdminManager::register_menu_item('Statistics', home_url . "admin/stats/");
?>