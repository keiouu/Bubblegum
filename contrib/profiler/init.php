<?php
/*
 * Tikapot Timer App
 *
 */

require_once(home_dir . "framework/signal_manager.php");
require_once(home_dir . "framework/timer.php");

function start_page_timer($request) {
	$request->pagetimer = Timer::start();
}

SignalManager::hook("page_load_start", "start_page_timer");
?>

