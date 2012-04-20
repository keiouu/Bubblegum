
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
		print '<table class="table table-bordered table-striped git-commit-file-changes">';
		print '<thead><tr><th colspan="3">'.$name.'</th></tr></thead>';
		print '<tbody>';
		$left_line = 0;
		$right_line = 0;
		foreach ($changes as $line) {
			print '<tr>';
			
			if (starts_with($line, '@@')) {
				print '<td>...</td><td>...</td><td><span style="color: #888;">' . $line . '</span></td>';
				$parts = explode(" ", $line);
				list($left_line, $left_length) = explode(",", $parts[1]);
				$left_line = substr($left_line, 1);
				list($right_line, $right_length) = explode(",", $parts[2]);
				$right_line = substr($right_line, 1);
			} else {
				if (starts_with($line, '+')) {
					print '<td></td><td>' . $right_line . '</td><td><span style="color: green;">' . $line . '</span></td>';
					$right_line += 1;
				} elseif (starts_with($line, '-')) {
					print '<td>' . $left_line . '</td><td></td><td><span style="color: red;">' . $line . '</span></td>';
					$left_line += 1;
				} else {
					print '<td>' . $left_line . '</td><td>' . $right_line . '</td><td>' . $line . '</td>';
					$left_line += 1;
					$right_line += 1;
				}
			}
			
			print '</tr>';
		}
		print '</tbody></table>';
	}
}
?>
