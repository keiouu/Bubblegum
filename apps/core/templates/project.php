<?php
require_once(home_dir . "apps/core/models.php");
?>
{% extends "apps/core/templates/base.php" %}

{% block endbody %}
<script type="text/javascript">
var project_id = <?php print $request->project->pk; ?>;
</script>
<script src="{{home_url}}apps/core/media/js/project.feeds.js"></script>
{% endblock endbody %}
    
{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1><?php print $request->project->name; ?></h1>
	</div>
	<p><?php print $request->project->description; ?></p>
	<hr />
	
	<h4><i class="icon-gift"></i> Milestones</h4>
	<br />
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Progress</th>
			</tr>
		</thead>
		<tbody id="milestone_feed">
			<tr><td colspan="2">Loading...</td></tr>
		</tbody>
	</table>
	<div class="pagination" data-link="milestone_feed">
		<ul>
			<li class="prev disabled"><a href="#" onClick="return false;">&larr; Prev</a></li>
			<span class="pages"></span>
			<li class="next disabled"><a href="#" onClick="return false;">Next &rarr;</a></li>
		</ul>
	</div>
	<hr />
	
	<h4><i class="icon-check"></i> Your Tasks</h4>
	<br />
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Milestone</th>
				<th>Name</th>
				<th>Type</th>
				<th>Priority</th>
				<th>Progress</th>
			</tr>
		</thead>
		<tbody id="tasks_feed">
			<tr><td colspan="5">Loading...</td></tr>
		</tbody>
	</table>
	<div class="pagination" data-link="tasks_feed">
		<ul>
			<li class="prev disabled"><a href="#" onClick="return false;">&larr; Prev</a></li>
			<span class="pages"></span>
			<li class="next disabled"><a href="#" onClick="return false;">Next &rarr;</a></li>
		</ul>
	</div>
	<hr />
	
	<h4><i class="icon-th-list"></i> All Tasks</h4>
	<br />
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Milestone</th>
				<th>Name</th>
				<th>Type</th>
				<th>Priority</th>
				<th>Progress</th>
				<th>Assigned To</th>
			</tr>
		</thead>
		<tbody id="all_tasks_feed">
			<tr><td colspan="6">Loading...</td></tr>
		</tbody>
	</table>
	<div class="pagination" data-link="all_tasks_feed">
		<ul>
			<li class="prev disabled"><a href="#" onClick="return false;">&larr; Prev</a></li>
			<span class="pages"></span>
			<li class="next disabled"><a href="#" onClick="return false;">Next &rarr;</a></li>
		</ul>
	</div>
</div>
{% endblock body %}
