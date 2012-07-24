<?php
require_once(home_dir . "apps/core/models.php");
require_once(home_dir . "apps/core/forms.php");
?>
{% extends "apps/core/templates/base.php" %}

{% block endbody %}
<script type="text/javascript">
var project_id = <?php print $request->project->pk; ?>;

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
</script>
{% endblock endbody %}
    
{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1><?php print $request->project->name; 
		 if ($request->project->tracked_by($request->user)) { ?>
			<a href="#" id="track" title="Untrack this project"><i class="icon-eye-close"></i></a>
		<?php } else { ?>
			<a href="#" id="track" title="Track this project"><i class="icon-eye-open"></i></a>
		<?php } ?></h1>
	</div>
	<p style="padding-left: 10px;"><?php print $request->project->description; ?></p>
	<hr />
	
	<div class="accordion">
		<?php 
		$git = $request->project->getRepository();
		if ($git !== null) {
			$git_data = $git->log();
			if ($git_data) {
		?>
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#gitCollapse">
				<h4><i class="icon-gift"></i> Git</h4>
			</a>
		</div>
		<div id="gitCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
				<?php include_once(home_dir . "apps/core/templates/includes/git-view.php"); ?>
			</div>
		</div>
		<?php }
		} ?>
		<div class="accordion-heading">
			<h4><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#milestoneCollapse"><i class="icon-gift"></i> Milestones</a> <a data-toggle="modal" href="#milestone-add"><i class="icon-plus" title="New Milestone"></i></a></h4>
		</div>
		<div id="milestoneCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
				<?php include_once(home_dir . "apps/core/templates/includes/project-milestones.php"); ?>
			</div>
		</div>
		<div class="accordion-heading">
			<h4><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tasksCollapse"><i class="icon-check"></i> Your Tasks</a> <a data-toggle="modal" href="#task-add"><i class="icon-plus" title="New Task"></i></a></h4>
		</div>
		<div id="tasksCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
				<?php include_once(home_dir . "apps/core/templates/includes/my-tasks.php"); ?>
			</div>
		</div>
		<div class="accordion-heading">
			<h4><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tasksCollapse"><i class="icon-th-list"></i> All Tasks</a> <a data-toggle="modal" href="#task-add"><i class="icon-plus" title="New Task"></i></a></h4>
		</div>
		<div id="allTasksCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
				<?php include_once(home_dir . "apps/core/templates/includes/task-list.php"); ?>
			</div>
		</div>
	</div>
	<?php include_once(home_dir . "apps/core/templates/includes/add-milestone.php"); ?>
	<?php include_once(home_dir . "apps/core/templates/includes/add-task.php"); ?>
	<?php include_once(home_dir . "apps/core/templates/includes/edit-task.php"); ?>
</div>
{% endblock body %}
