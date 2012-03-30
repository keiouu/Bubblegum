<table class="table table-bordered table-striped git-commit">
<?php
include_once(home_dir . "framework/utils.php");
include_once(home_dir . "framework/model_fields/datetimefield.php");
$git_data = $git->log(2);
if ($git_data) {
	$dateField = new DateTimeField();
	$dateField->set_value(date("Y-m-d H:i:s", $git_data["date"]));
	$gravatar = md5(strtolower(trim($git_data["email"])));
	print '<thead><tr><th>' . $git_data["message"] . '</th></tr></thead>';
	print '<tbody><tr><td><span class="author"><img src="http://www.gravatar.com/avatar/'.$gravatar.'?s=22" alt="Profile Image" />' . $git_data["author"] . '</span> <span class="date">(' . $dateField->get_readable_value() . ')</span>';
	print '<a href="'.$request->fullPath.'git/'.$git_data["hash"].'/" class="hash">' . ellipsize($git_data["hash"], 13) . '</a></td></tr></tbody>';
} else {
	print '<td>No commits!</td>';
}
?>
</table>
