<?php
/**
 * URLS
 *
 */

require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/views.php");

new StatisticsAdminView("/admin/stats/", home_dir . "contrib/statistics/templates/stats.php", $GLOBALS["i18n"]["admin"]['admin'] . " | Tikapot");
?>