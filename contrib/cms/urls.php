<?php
/*
 * URLS
 *
 */

require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/views.php");
require_once(dirname(__FILE__) . "/models.php");

foreach (CMS_Page::objects()->all() as $page) {
	new CMSView($page);
}
?>

