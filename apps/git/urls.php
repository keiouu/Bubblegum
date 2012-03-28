<?php
/*
 * URLS
 *
 */

require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/views.php");

new GitCommitReceiveView("/api/git/commit/receive/");
new GitAPIView("/api/git/tag/create/");
new GitAPIView("/api/git/tag/delete/");
new GitBranchCreateView("/api/git/branch/create/");
new GitBranchDeleteView("/api/git/branch/delete/");
new GitAPIView("/api/git/tracking-branch/create/");
new GitAPIView("/api/git/tracking-branch/delete/");
?>

