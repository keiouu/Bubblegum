<?php
/*
 * Tikapot Admin App URLs
 *
 */

require_once(home_dir . "contrib/admin/views.php");
require_once(home_dir . "framework/view.php");

// Load views
new AdminView("/admin/", home_dir . "contrib/admin/templates/index.php", $GLOBALS["i18n"]["admin"]['admin'] . " | Tikapot");
new AdminLoginView("/admin/login/", home_dir . "contrib/admin/templates/login.php", $GLOBALS["i18n"]["admin"]['admin_login'] . " | Tikapot");
new AdminRegisterView("/admin/register/", home_dir . "contrib/admin/templates/register.php", $GLOBALS["i18n"]["admin"]['admin_register'] . " | Tikapot");
new AdminConfigView("/admin/config/", home_dir . "contrib/admin/templates/viewconfig.php", $GLOBALS["i18n"]["admin"]['admin_edit_config'] . " | Tikapot");
new AdminUpgradeView("/admin/upgrade/", home_dir . "contrib/admin/templates/upgrade.php", $GLOBALS["i18n"]["admin"]['admin_upgrade'] . " | Tikapot");
new AdminUpdateView("/admin/update/", home_dir . "contrib/admin/templates/update.php", $GLOBALS["i18n"]["admin"]['admin_update'] . " | Tikapot");

?>

