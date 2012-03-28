{% extends "apps/core/templates/base.php" %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1>Welcome to {{project_name}}!</h1>
	</div>
	<p class="add-links">
		<a href="{{home_url}}projects/new/">Start new Project &raquo;</a>
		<a href="{{home_url}}projects/">View my Projects &raquo;</a>
	</p>
	<hr />
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
</div>
{% endblock body %}

{% block endbody %}
<script src="{{home_url}}apps/core/media/js/utils.js"></script>
<script src="{{home_url}}apps/core/media/js/project.feeds.js"></script>
<script src="{{home_url}}apps/core/media/js/feeds.ajax.js"></script>
{% endblock endbody %}
			<?php
			include_once(home_dir . "lib/tp-git/git.php");
			$git = new Git();
			print_r($git->log());
			?>

