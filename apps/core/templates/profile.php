{% extends "apps/core/templates/base.php" %}

{% block breadcrumbs %}
	<?php
	print '<li><a href="'.home_url.'">Home</a></li>';
	print ' <li class="divider">/</li><li class="active">Profile</li>';
	?>
{% endblock %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1><img src="<?php print $request->gravatar . "?s=32"; ?>" alt="Gravatar Image" class="gravatar" /> <?php print $request->profile; ?> <small><a href="{{logout_url}}" rel="popover" title="Logout" data-content="Are you sure?"><i class="icon-off"></i></a></small></h1>
	</div>
      
      <div class="span3 draggables c1 well">
      	<h3><i class="icon-align-left"></i> Backlog</h3>
      	<div class="inner">
      		<?php
      		require_once(home_dir . "apps/core/models.php");
      		$tasks = Task_Link::find(array("assignee" => $request->user));
			$count = 0;
			foreach ($tasks as $task) {
				if ($task->task->status == $task->task->_status["New"] || $task->task->status == $task->task->_status["Confirmed"]) {
					$count++;
					print '	<div class="draggable btn btn-primary">
				      			<p class="title">'.$task->task->name.'</p>
				      			<p class="author">'.$task->task->updated.'</p>
			      			</div>';
				}
			}
      		?>
      	</div>
      </div>

      <div class="span3 draggables c1 well">
      	<h3><i class="icon-retweet"></i> In Progress</h3>
      	<div class="inner">
      		<?php
			$count = 0;
			foreach ($tasks as $task) {
				if ($task->task->status == $task->task->_status["In Progress"] || $task->task->status == $task->task->_status["Testing"] || $task->task->status == $task->task->_status["Awaiting Feedback"]) {
					$count++;
					print '	<div class="draggable btn btn-primary">
						      	<p class="title">'.$task->task->name.'</p>
						      	<p class="author">'.$task->task->updated.'</p>
					      	</div>';
				}
			}
      		?>
      	</div>
      </div>
      
      <div class="span3 draggables c1 well">
      	<h3><i class="icon-ok"></i> Complete</h3>
      	<div class="inner">
      		<?php
			$count = 0;
			foreach ($tasks as $task) {
				if ($task->task->status == $task->task->_status["Complete"]) {
					$count++;
					print '	<div class="draggable btn btn-primary">
				      			<p class="title">'.$task->task->name.'</p>
				      			<p class="author">'.$task->task->updated.'</p>
			      			</div>';
				}
				if ($count >= 10)
					break;
			}
      		?>
      	</div>
      </div>
</div>
{% endblock body %}

