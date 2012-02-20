<?php
/*
 * Admin
 *
 */

require_once(home_dir . "contrib/admin/core.php");
require_once(dirname(__FILE__) . "/models.php");
require_once(dirname(__FILE__) . "/forms.php");

AdminModel::register("notes", new Admin_Note(), new AdminNoteForm());
?>

