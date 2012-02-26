<?php
require_once(home_dir . "apps/core/models.php");
require_once(home_dir . "apps/core/forms.php");
?>
{% extends "apps/core/templates/base.php" %}

{% block endbody %}
<script type="text/javascript">
var project_id = <?php print $request->project->pk; ?>;
</script>
<script src="{{home_url}}apps/core/media/js/utils.js"></script>
<script src="{{home_url}}apps/core/media/js/project.feeds.js"></script>
<script src="{{home_url}}apps/core/media/js/feeds.ajax.js"></script>
{% endblock endbody %}
    
{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1><?php print $request->project->name; ?></h1>
	</div>
	<p style="padding-left: 10px;"><?php print $request->project->description; ?></p>
	<p class="add-links">
		<a data-toggle="modal" href="#milestone-add">Add new Milestone &raquo;</a>
		<a data-toggle="modal" href="#task-add">Add new Task &raquo;</a>
	</p>
	<hr />
	
	<div class="accordion">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#milestoneCollapse">
				<h4><i class="icon-gift"></i> Milestones</h4>
			</a>
		</div>
		<div id="milestoneCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
				<?php include_once(home_dir . "apps/core/templates/includes/project-milestones.php"); ?>
			</div>
		</div>
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tasksCollapse">
				<h4><i class="icon-check"></i> Your Tasks</h4>
			</a>
		</div>
		<div id="tasksCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
				<?php include_once(home_dir . "apps/core/templates/includes/my-tasks.php"); ?>
			</div>
		</div>
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#allTasksCollapse">
				<h4><i class="icon-th-list"></i> All Tasks</h4>
			</a>
		</div>
		<div id="allTasksCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
				<?php include_once(home_dir . "apps/core/templates/includes/task-list.php"); ?>
			</div>
		</div>
	</div>
	<?php include_once(home_dir . "apps/core/templates/includes/add-milestone.php"); ?>
	<?php include_once(home_dir . "apps/core/templates/includes/edit-task.php"); ?>
</div>
{% endblock body %}
