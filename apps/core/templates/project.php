<?php
require_once(home_dir . "apps/core/models.php");
require_once(home_dir . "apps/core/forms.php");
$git = $request->project->getRepository();
if ($git !== null) {
	$git_data = $git->log();
}
?>
{% extends "apps/core/templates/base.php" %}

{% block endbody %}
<script type="text/javascript">
var project_id = <?php print $request->project->pk; ?>;
$(function() {	
	$("#track").click(function() {
		$.ajax({
		  <?php if ($request->project->tracked_by($request->user)) { ?>
		  url: tp_home_url + "api/project/<?php print $request->project->pk; ?>/track/",
		  <?php } else { ?>
		  url: tp_home_url + "api/project/<?php print $request->project->pk; ?>/untrack/",
		  <?php } ?>
		  success: function(data) {
	       window.location = window.location;
		  }
		});
		return false;
	});
});
</script>
{% endblock endbody %}
    
{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1><?php
		print $request->project->name; 
		if ($request->project->tracked_by($request->user)) {
			print ' <a href="#" id="track" rel="tooltip" title="Untrack this project"><i class="icon-eye-close"></i></a>';
		} else {
			print ' <a href="#" id="track" rel="tooltip" title="Track this project"><i class="icon-eye-open"></i></a>';
		}
		print ' <a href="#" id="track" rel="popover" title="'.$request->project->name.'" data-content="'.$request->project->description.'"><i class="icon-question-sign"></i></a>';
		?></h1>
	</div>
	
	<ul class="nav nav-pills">
		<?php
			$tabs = array("dashboard", "tasks");
			if ($git && $git_data) {
				$tabs[] = "code";
				$tabs[] = "deployments";
			}
			foreach ($tabs as $tab) {
				$class = (!isset($_GET['tab']) && $tab == $tabs[0]) || (isset($_GET['tab']) && $_GET['tab'] == $tab) ? ' class="active"' : '';
				print '<li'.$class.'><a href="?tab='.$tab.'">'.ucwords($tab).'</a></li>';
			}
		?>
	</ul>
	
	<div class="accordion">
		<?php
		if (isset($_GET['tab']) && $_GET['tab'] == "code") {
			if ($git !== null) {
				if ($git_data) {
		?>
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#gitCollapse">
							<h4><i class="icon-gift"></i> Latest Commit</h4>
						</a>
					</div>
					<div id="gitCollapse" class="accordion-body in collapse" style="height: auto;">
						<div class="accordion-inner">
							<?php include_once(home_dir . "apps/core/templates/includes/git-view.php"); ?>
						</div>
					</div>
		<?php  } ?>
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#gitcodeCollapse">
						<h4><i class="icon-hdd"></i> Code Browser</h4>
					</a>
				</div>
				<div id="gitcodeCollapse" class="accordion-body in collapse" style="height: auto;">
					<div class="accordion-inner">
						<?php include_once(home_dir . "apps/core/templates/includes/git-file-list.php"); ?>
					</div>
				</div>
		<?php
			}
		}
		if (!isset($_GET['tab']) || $_GET['tab'] == "dashboard") {
		?>
			<div class="accordion-heading">
				<h4><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#milestoneCollapse"><i class="icon-gift"></i> Milestones</a> <a data-toggle="modal" href="#milestone-add" rel="tooltip" title="New Milestone"><i class="icon-plus"></i></a></h4>
			</div>
			<div id="milestoneCollapse" class="accordion-body in collapse" style="height: auto;">
				<div class="accordion-inner">
					<?php include_once(home_dir . "apps/core/templates/includes/project-milestones.php"); ?>
				</div>
			</div>
		<?php
		}
		if (!isset($_GET['tab']) || $_GET['tab'] == "dashboard" || $_GET['tab'] == "tasks" ) {
		?>
		<div class="accordion-heading">
			<h4><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tasksCollapse"><i class="icon-check"></i> Your Tasks</a> <a data-toggle="modal" href="#task-add" rel="tooltip" title="New Task"><i class="icon-plus"></i></a></h4>
		</div>
		<div id="tasksCollapse" class="accordion-body in collapse" style="height: auto;">
			<div class="accordion-inner">
				<?php include_once(home_dir . "apps/core/templates/includes/my-tasks.php"); ?>
			</div>
		</div>
		<?php
		}
		if (isset($_GET['tab']) && $_GET['tab'] == "tasks") {
		?>
			<div class="accordion-heading">
				<h4><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tasksCollapse"><i class="icon-th-list"></i> All Tasks</a> <a data-toggle="modal" href="#task-add" rel="tooltip" title="New Task"><i class="icon-plus"></i></a></h4>
			</div>
			<div id="allTasksCollapse" class="accordion-body in collapse" style="height: auto;">
				<div class="accordion-inner">
					<?php include_once(home_dir . "apps/core/templates/includes/task-list.php"); ?>
				</div>
			</div>
		<?php
		}
		if (isset($_GET['tab']) && $_GET['tab'] == "deployments") {
		?>
			<div class="accordion-heading">
				<h4><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#deploymentsCollapse"><i class="icon-th-list"></i> Deployments</a> <a data-toggle="modal" href="#deployments-add" rel="tooltip" title="New Deployment"><i class="icon-plus"></i></a></h4>
			</div>
			<div id="deploymentsCollapse" class="accordion-body in collapse" style="height: auto;">
				<div class="accordion-inner">
				</div>
			</div>
		<?php 
			include_once(home_dir . "apps/core/templates/includes/new-deployment.php");
		}
		?>
	</div>
	<?php
	include_once(home_dir . "apps/core/templates/includes/add-milestone.php");
	include_once(home_dir . "apps/core/templates/includes/add-task.php");
	include_once(home_dir . "apps/core/templates/includes/edit-task.php");
	?>
</div>
{% endblock body %}
