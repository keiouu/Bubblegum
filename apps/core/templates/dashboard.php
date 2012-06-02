{% extends "apps/core/templates/base.php" %}

{% block style %}
<link href="{{home_url}}apps/core/media/css/dashboard.css" rel="stylesheet">
{% endblock %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1>Dashboard</h1>
	</div>
      
      <div style="height: 25px;width: 100%;"></div>
      
      <div class="span3 draggables c1">
      	<h3>Backlog</h3>
      	<div class="inner">
      		<?php
      		require_once(home_dir . "apps/core/models.php");
      		$tasks = Task_Link::find(array("assignee" => $request->user));
			foreach ($tasks as $task) {
				if ($task->task->status == $task->task->_status["New"] || $task->task->status == $task->task->_status["Confirmed"]) {
					print '	<div class="draggable btn btn-primary">
				      			<p class="title">'.$task->task->name.'</p>
				      			<p class="author">'.$task->task->updated.'</p>
			      			</div>';
				}
			}
      		?>
      	</div>
      </div>

      <div class="span3 draggables c1">
      	<h3>In Progress</h3>
      	<div class="inner">
      		<?php
			foreach ($tasks as $task) {
				if ($task->task->status == $task->task->_status["In Progress"] || $task->task->status == $task->task->_status["Testing"] || $task->task->status == $task->task->_status["Awaiting Feedback"]) {
					print '	<div class="draggable btn btn-primary">
						      	<p class="title">'.$task->task->name.'</p>
						      	<p class="author">'.$task->task->updated.'</p>
					      	</div>';
				}
			}
      		?>
      	</div>
      </div>
      
      <div class="span3 draggables c1">
      	<h3>Complete</h3>
      	<div class="inner">
      		<?php
			foreach ($tasks as $task) {
				if ($task->task->status == $task->task->_status["Complete"]) {
					print '	<div class="draggable btn btn-primary">
				      			<p class="title">'.$task->task->name.'</p>
				      			<p class="author">'.$task->task->updated.'</p>
			      			</div>';
				}
			}
      		?>
      	</div>
      </div>
    
</div>
{% endblock body %}

{% block endbody %}
<script src="{{home_url}}apps/core/media/js/utils.js"></script>
<script src="{{home_url}}apps/core/media/js/project.feeds.js"></script>
<script src="{{home_url}}apps/core/media/js/feeds.ajax.js"></script>
<script src="{{home_url}}apps/core/media/js/jquery.drag.js"></script>
<script src="{{home_url}}apps/core/media/js/jquery.utils.js"></script>
<script src="{{home_url}}apps/core/media/js/dashboard.js"></script>
{% endblock endbody %}

