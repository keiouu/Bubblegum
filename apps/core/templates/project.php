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
		</tbody>
	</table>
	<div class="pagination">
		<ul>
			<li class="prev disabled"><a href="/admin/Users/Team/?" onClick="return false;">&larr; Prev</a></li><li class="active"><a href="/admin/Users/Team/?page=1">1</a></li><li class="next disabled"><a href="/admin/Users/Team/?" onClick="return false;">Next &rarr;</a></li>		</ul>
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
		<tbody>
			<?php
			$tasks = Task::objects()->filter(array("project" => $request->project->pk));
			foreach ($tasks as $task) {
				if ($task->progress >= 100)
					continue;
				
				print '<tr>
					<td>'.$task->milestone.'</td>
					<td>'.$task->name.'</td>
					<td>'.$task->_type.'</td>
					<td>'.$task->_priority.'</td>
					<td>
						<div class="progress progress-'.($task->progress <= 25 ? 'danger' : ($task->progress >= 75 ? 'success' : 'info')).' progress-striped active">
							<div class="progress-text">'.$task->progress.'%</div>
							<div class="bar" style="width:'.$task->progress.'%;"></div>
						</div>
					</td>
					<td>'.$task->assignees().'</td>
				</tr>';
			}
			if ($tasks->count() == 0)
				print '<tr><td colspan="6">No Data!</td></tr>';
			?>
		</tbody>
	</table>
	<div class="pagination">
		<ul>
			<li class="prev disabled"><a href="/admin/Users/Team/?" onClick="return false;">&larr; Prev</a></li><li class="active"><a href="/admin/Users/Team/?page=1">1</a></li><li class="next disabled"><a href="/admin/Users/Team/?" onClick="return false;">Next &rarr;</a></li>		</ul>
	</div>
</div>
{% endblock body %}
