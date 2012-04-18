<?php
/*
 * URLS
 *
 */

require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/views.php");

new BaseView("/", home_dir . "apps/core/templates/index.php", "Home Page");
new LoginView("/login/", home_dir . "apps/core/templates/login.php", "Login Page");
new BaseView("/projects/", home_dir . "apps/core/templates/projects.php", "Projects");
new ProjectView("/projects/(?P<project>\d+)/", home_dir . "apps/core/templates/project.php", "Project");
new SupportView("/support/", home_dir . "apps/core/templates/support/404.php", "Support");
new BaseView("/projects/new/", home_dir . "apps/core/templates/new-project.php", "New Project");
new ProfileView("/profile/", home_dir . "apps/core/templates/profile.php", "Your Profile");
new ProfileView("/profile/(?P<user>\d+)/", home_dir . "apps/core/templates/profile.php", "Profile");

// Git
new Git_CommitView("/projects/(?P<project>\d+)/git/(?P<ref>[a-z\d]+)/", home_dir . "apps/core/templates/git_commit.php", "Commit");

// API
new AJAX_ActivityFeedView("/api/activity_feed/");
new AJAX_MileStonesView("/api/project/(?P<project>\d+)/milestones/");
new AJAX_MileStoneAddView("/api/project/(?P<project>\d+)/milestones/add/");
new AJAX_TasksView("/api/project/(?P<project>\d+)/tasks/");
new AJAX_TaskDetailView("/api/project/(?P<project>\d+)/task/detail/");
new AJAX_ProjectDetailView("/api/project/(?P<project>\d+)/detail/");
new AJAX_TaskEditView("/api/project/(?P<project>\d+)/task/edit/");
new AJAX_TaskAddView("/api/project/(?P<project>\d+)/task/add/");
new AJAX_ProjectTrackView("/api/project/(?P<project>\d+)/track/");
new AJAX_ProjectUnTrackView("/api/project/(?P<project>\d+)/untrack/");

// New API
new AJAX_TasksView("/api/tasks/");
?>

