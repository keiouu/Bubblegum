<?php
/*
 * Init Script
 *
 * The purpose of this app is to log admin actions
 * for review/etc
 *
 */
 
require_once(home_dir . "framework/signal_manager.php");
require_once(dirname(__FILE__) . "/models.php");

// Log Model Creations
function admin_log_on_create($args) {
	list($user, $obj) = $args;
	$message = "Created new ".get_class($obj)." (PK: ".$obj->pk."): ";
	if (AdminManager::is_class_registered(get_class($obj))) 
		$message .= '<a href="'.AdminManager::get_edit_link($obj).'">Link</a>';
	else
		$message .= $obj;
	Admin_log::create(array("user" => $user, "action" => "0", "detail" => $message));
}
SignalManager::hook("admin_on_create", "admin_log_on_create");

// Log Model Edits
function admin_log_on_edit($args) {
	list($user, $obj) = $args;
	$message = "Edited ".get_class($obj)." (PK: ".$obj->pk."): ";
	if (AdminManager::is_class_registered(get_class($obj))) 
		$message .= '<a href="'.AdminManager::get_edit_link($obj).'">Link</a>';
	else
		$message .= $obj;
	Admin_log::create(array("user" => $user, "action" => "1", "detail" => $message));
}
SignalManager::hook("admin_on_edit", "admin_log_on_edit");

// Log Model Deletions
function admin_log_on_delete($args) {
	list($user, $obj) = $args;
	Admin_log::create(array("user" => $user, "action" => "2", "detail" => "Deleted ".get_class($obj)." (PK: ".$obj->pk."): " . $obj));
}
SignalManager::hook("admin_on_delete", "admin_log_on_delete");

// Log Admin Login
function admin_log_login($user) {
	Admin_log::create(array("user" => $user, "action" => "3"));
}
SignalManager::hook("admin_on_login", "admin_log_login");

// Log Admin Register
function admin_log_register($user) {
	Admin_log::create(array("user" => $user, "action" => "4"));
}
SignalManager::hook("admin_on_register", "admin_log_register");

// Log Admin Updates
function admin_log_update($request) {
	Admin_log::create(array("user" => $request->user, "action" => "5"));
}
SignalManager::hook("admin_on_update", "admin_log_update");

// Log Admin Upgrades
function admin_log_upgrade($request) {
	Admin_log::create(array("user" => $request->user, "action" => "6"));
}
SignalManager::hook("admin_on_upgrade", "admin_log_upgrade");

?>

