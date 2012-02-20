<?php
/*
 * Init Script
 *
 */

// TODO - AJAX

require_once(home_dir . "framework/signal_manager.php");
require_once(dirname(__FILE__) . "/forms.php");
require_once(dirname(__FILE__) . "/models.php");

function bug_init($request) {
	$request->bug_report_form = new BugAddForm();
	if (isset($request->get['bugreport']) && isset($request->post['control_formid']) && isset($request->post[$request->post['control_formid']."_content"])) {
		$request->bug_report_form->load_post_data($request->post);
		$bug = $request->bug_report_form->save(new Bug(), $request);
		if ($bug) {
			$bug->reported_by = $request->user;
			$bug->save();
			$request->message("Thank you! We will look into it shortly...", "info");
		}
	}
}

SignalManager::hook("page_load_start", "bug_init");
?>

