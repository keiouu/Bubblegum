
<?php
include_once(home_dir . "framework/utils.php");

if (!isset($git_changes) || !isset($git_file_changes)) {
	$git_changes = $git->files_changed();
	$git_file_changes = $git->file_changes();
}

if (!isset($project) && isset($request->project))
	$project = $request->project;

print '<table class="table table-bordered table-striped git-commit">';
print '<thead><tr><th colspan="2">Files Changed</th></tr></thead>';
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
		print '<tr><td>'.$status.'</td><td>'.string_encode($change['file']).'</td></tr>';
	}
} else {
	print '<td>No files were changed!</td>';
}

print '</tbody></table>';

if (isset($git_file_changes)) {
	foreach ($git_file_changes as $name => $changes) {
		print '<div class="git-commit-file-changes">';
		print '<h3>'.string_encode($name).'</h3>';
		print '<pre class="code list">';
		$left_line = 0;
		$right_line = 0;
		foreach ($changes as $line) {
			if (starts_with($line, '@@')) {
				$parts = explode(" ", $line);
				list($left_line, $left_length) = explode(",", $parts[1]);
				$left_line = substr($left_line, 1);
				list($right_line, $right_length) = explode(",", $parts[2]);
				$right_line = substr($right_line, 1);
				print '<p class="grey R-- L--"><span>' . $line . '</span></p>';
			} else {
				if (starts_with($line, '+')) {
					print '<p class="R'.$right_line.'"><code class="code-green">' . string_encode($line) . '</code></p>';
					$right_line += 1;
				} elseif (starts_with($line, '-')) {
					print '<p class="L'.$left_line.'"><code class="code-red">' . string_encode($line) . '</code></p>';
					$left_line += 1;
				} else {
					print '<p class="L'.$left_line.' R'.$right_line.'"><code>' . string_encode($line) . '</code></p>';
					$left_line += 1;
					$right_line += 1;
				}
			}
			
		}
		print '</pre>';
		print '</div>';
	}
}
?>
