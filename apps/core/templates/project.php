<?php
require_once(home_dir . "apps/core/models.php");
require_once(home_dir . "apps/core/forms.php");
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
	<p style="padding-left: 10px;"><?php print $request->project->description; ?></p>
	<hr />
	
	<div class="accordion">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#milestoneCollapse">
				<h4><i class="icon-gift"></i> Milestones</h4>
			</a>
		</div>
		<div id="milestoneCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
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
				<p class="addlink"><a data-toggle="modal" href="#milestone-add">Add new Milestone &raquo;</a></p>
				<div class="pagination" data-link="milestone_feed">
					<ul>
						<li class="prev disabled"><a href="#" onClick="return false;">&larr; Prev</a></li>
						<span class="pages"></span>
						<li class="next disabled"><a href="#" onClick="return false;">Next &rarr;</a></li>
					</ul>
				</div>
				<div class="modal" id="milestone-add">
				  <div class="modal-header">
					 <a class="close" data-dismiss="modal">×</a>
					 <h3>Add a new Milestone</h3>
				  </div>
				  <div class="modal-body">
					 <?php
					 $form = new MileStoneAddForm();
					 $form->display();
					 ?>
				  </div>
				  <div class="modal-footer">
					 <a href="#" class="btn btn-primary btn-save">Save</a>
					 <a href="#" class="btn" data-dismiss="modal">Close</a>
				  </div>
				</div>
			</div>
		</div>
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tasksCollapse">
				<h4><i class="icon-check"></i> Your Tasks</h4>
			</a>
		</div>
		<div id="tasksCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
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
			</div>
		</div>
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#allTasksCollapse">
				<h4><i class="icon-th-list"></i> All Tasks</h4>
			</a>
		</div>
		<div id="allTasksCollapse" class="accordion-body in collapse" style="height: auto; ">
			<div class="accordion-inner">
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
		</div>
	</div>
	<div class="modal" id="task-edit">
	  <div class="modal-header">
		 <a class="close" data-dismiss="modal">×</a>
		 <h3 class="task-title"></h3>
	  </div>
	  <div class="modal-body">
			<form action="" method="post" id="task-edit-form" class="form-horizontal">
		  		<fieldset>
					<input type="hidden" name="csrf" id="csrf-token" value="{{csrf_token}}" />
					<input type="hidden" name="pk" id="task-pk" value="" />
					<div class="control-group">
						<label class="control-label" for="task-name">Task Name</label>
				  		<div class="controls">
					  		<input name="name" id="task-name" />
					  	</div>
				  	</div>
					<div class="control-group">
						<label class="control-label" for="task-description">Task Description</label>
				  		<div class="controls">
					  		<textarea name="description" id="task-description"></textarea>
					  	</div>
				  	</div>
					<div class="control-group">
						<label class="control-label" for="task-progress">Task Progress</label>
				  		<div class="controls">
					  		<div class="input-append">
						  		<input name="progress" id="task-progress" class="span1" />
						  		<span class="add-on">%</span>
						  	</div>
					  	</div>
				  	</div>
					<div class="control-group">
						<label class="control-label" for="task-milestone">MileStone</label>
				  		<div class="controls">
						  	<select name="milestone" id="task-milestone">
						  		
						  	</select>
					  	</div>
				  	</div>
		  		</fieldset>
	  		</form>
	  </div>
	  <div class="modal-footer">
		 <button class="btn btn-primary btn-save">Save</button>
		 <button class="btn" data-dismiss="modal">Close</button>
	  </div>
	</div>
</div>
{% endblock body %}
