<?php
// This is a list of files/directories in the git repo
// We assume some clever JS will handle it.

include_once(home_dir . "framework/utils.php");

function print_listing($listing) {
	print '<ul>';
	krsort($listing);
	foreach ($listing as $key => $val) {
		if (is_array($val)) {
			print '<li>' . $key;
			print_listing($val);
			print '</li>';
		} else {
			print '<li>'.$val.'</li>';
		}
	}
	print '</ul>';
}

print '<div class="git-file-list">';
print_listing($git->ls());
print '</div>';
