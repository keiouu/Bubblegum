<?php
/*
 * URLS
 *
 */

require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/views.php");

new LoginView("/login/", home_dir . "apps/core/templates/login.php", "Login Page");
new BaseView("/", home_dir . "apps/core/templates/index.php", "Home Page");
new ProjectView("/projects/", home_dir . "apps/core/templates/projects.php", "Projects");
new ProjectView("/projects/(?P<project>\d+)/", home_dir . "apps/core/templates/project.php", "Project");
?>

