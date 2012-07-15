<table class="table table-bordered table-striped git-commit">
<?php
include_once(home_dir . "framework/utils.php");
include_once(home_dir . "framework/model_fields/datetimefield.php");

if (!isset($git_data))
	$git_data = $git->log();

if (!isset($project) && isset($request->project))
	$project = $request->project;

if ($git_data && $project) {
	$dateField = new DateTimeField();
	$dateField->set_value(date("Y-m-d H:i:s", $git_data["date"]));
	$gravatar = md5(strtolower(trim($git_data["email"])));
	print '<thead><tr><th><a href="'.$request->fullPath.'git/'.$git_data["hash"].'/">' . string_encode($git_data["message"]) . '</a></th></tr></thead>';
	print '<tbody><tr><td><span class="author"><img src="http://www.gravatar.com/avatar/'.$gravatar.'?s=22" alt="Profile Image" />' . string_encode($git_data["author"]) . '</span> <span class="date">(' . $dateField->get_readable_value() . ')</span>';
	if (strlen($git_data["parent"]) > 0) {
		print '<span class="hash"><a href="'.home_url.'projects/'.$project->pk.'/git/'.$git_data["parent"].'/">' . ellipsize($git_data["parent"], 13) . '<i class="icon-share-alt"></i></a></span>';
	}
	if (!isset($show_ref) || $show_ref) {
		print '<span class="hash"><a href="'.home_url.'projects/'.$project->pk.'/git/'.$git_data["hash"].'/">' . ellipsize($git_data["hash"], 13) . '<i class="icon-eye-open"></i></a></span>';
	}
	print '</td></tr></tbody>';
} else {
	print '<td>No commits!</td>';
}
?>
</table>
