<?php
/*
 * URLS
 *
 */

require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/views.php");

new TemplateView("/login/", home_dir . "apps/core/templates/login.php", "Login Page");
new BaseView("/", home_dir . "apps/core/templates/index.php", "Home Page");
?>

