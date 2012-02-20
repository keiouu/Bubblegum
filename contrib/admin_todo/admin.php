<?php
/*
 * Tikapot Todo App Admin Registration
 *
 */

require_once(home_dir . "contrib/admin/core.php");
require_once(home_dir . "contrib/admin_todo/models.php");
require_once(home_dir . "contrib/admin_todo/forms.php");

AdminModel::register("todo", new Todo_Item(), new TodoItemForm(), new TodoItemEditForm(), null, array("content"), array("priority" => "", "completed" => "0", "assigned_to" => "", "completed_by" => ""));
?>

