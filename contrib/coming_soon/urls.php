<?php
/*
 * URLS
 *
 */

require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/views.php");

new ComingSoonView("/", home_dir . "contrib/coming_soon/templates/index.php", $GLOBALS['i18n']['coming_soon']['title']);
?>

