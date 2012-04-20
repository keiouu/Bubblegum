
<?php
include_once(home_dir . "framework/utils.php");

if (!isset($git_changes) || !isset($git_file_changes)) {
	$git_changes = $git->files_changed();
	$git_file_changes = $git->file_changes();
}

if (!isset($project) && isset($request->project))
	$project = $request->project;

print '<table class="table table-bordered table-striped git-commit">';
print '<thead><tr><th>Files Changed</th></tr></thead>';
print '<tbody>';

if (isset($git_changes)) {
	foreach ($git_changes as $change) {
		$status = $change['status'];
		if ($status == "A") {
			$status = '<i class="icon-plus"></i>';
		} elseif ($status == "M") {
			$status = '<i class="icon-adjust"></i>';
		} elseif ($status == "D") {
			$status = '<i class="icon-minus"></i>';
		} else { // Is there one for binary?
			// Flag sysadmin, the world is coming to an end.
		}
		print '<tr><td>'.$status.'</td><td>'.$change['file'].'</td></tr>';
	}
} else {
	print '<td>No files were changed!</td>';
}

print '</tbody></table>';

if (isset($git_file_changes)) {
	foreach ($git_file_changes as $name => $changes) {
		print '<table class="table table-bordered table-striped git-commit">';
		print '<thead><tr><th>'.$name.'</th></tr></thead>';
		print '<tbody>';
		foreach ($changes as $line) {
			print '<tr><td>';
			if (starts_with($line, '+'))
				print '<span style="color: green;">' . $line . '</span>';
			elseif (starts_with($line, '-'))
				print '<span style="color: red;">' . $line . '</span>';
			else
				print $line;
			print '</td></tr>';
		}
		print '</tbody></table>';
	}
}
?>
