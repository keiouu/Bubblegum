<?php
/*
 * Init Script
 *
 */

require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "framework/signal_manager.php");
require_once(dirname(__FILE__) . "/urls.php");

ConfigManager::register_app_config("coming_soon", "release_date", "dd/mm/yy hh:mm");
?>

