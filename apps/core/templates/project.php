<?php
require_once(home_dir . "apps/core/models.php");
?>
{% extends "apps/core/templates/base.php" %}

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
		<tbody>
			<?php
			$milestones = Milestone::objects()->filter(array("project" => $request->project->pk));
			foreach ($milestones as $milestone) {
				$total_progress = 0;
				$tasks = Task::objects()->filter(array("milestone" => $milestone->pk));
				foreach ($tasks as $task) {
					$total_progress += $task->progress;
				}
				$max_progress = $tasks->count() * 100;
				$progress = ($total_progress / $max_progress) * 100;
				print '<tr>
					<td>'.$milestone->name.'</td>
					<td>
						<div class="progress progress-'.($progress <= 25 ? 'danger' : ($progress >= 75 ? 'success' : 'info')).' progress-striped active">
							<div class="progress-text">'.$progress.'%</div>
							<div class="bar" style="width:'.$progress.'%;"></div>
						</div>
					</td>
				</tr>';
			}
			if ($milestones->count() == 0)
				print '<tr><td colspan="2">No Data!</td></tr>';
			?>
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
		<tbody>
			<?php
			$tasks = Task::objects()->filter(array("project" => $request->project->pk));
			foreach ($tasks as $task) {
				if ($task->progress >= 100 || !$task->assigned($request->user))
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
				</tr>';
			}
			if ($milestones->count() == 0)
				print '<tr><td colspan="5">No Data!</td></tr>';
			?>
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
			if ($milestones->count() == 0)
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
