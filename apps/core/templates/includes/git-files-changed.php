<table class="table table-bordered table-striped git-commit">
<?php
include_once(home_dir . "framework/utils.php");

if (!isset($git_changes))
	$git_changes = $git->files_changed();

if (!isset($project) && isset($request->project))
	$project = $request->project;

if ($git_changes && $project) {
	print '<thead><tr><th>Files Changed</th></tr></thead>';
	print '<tbody>';
	foreach ($git_changes as $change) {
		$status = $change['status'];
		if ($status == "A") {
			$status = '<i class="icon-plus"></i>';
		} elseif ($status == "M") {
			$status = '<i class="icon-adjust"></i>';
		} elseif ($status == "D") {
			$status = '<i class="icon-minus"></i>';
		} else {
			// Flag sysadmin
		}
		print '<tr><td>'.$status.'</td><td>'.$change['file'].'</td></tr>';
	}
	print '</tbody>';
} else {
	print '<td>No files were changed!</td>';
}
?>
</table>
