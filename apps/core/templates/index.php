{% extends "apps/core/templates/base.php" %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1>Welcome to {{project_name}}!</h1>
	</div>
	
	<div class="accordion-heading">
		<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#tasksCollapse">
			<h4><i class="icon-check"></i> Your Tasks</h4>
		</a>
	</div>
	<div id="tasksCollapse" class="accordion-body in collapse" style="height: auto; ">
		<div class="accordion-inner">
			<?php include_once(home_dir . "apps/core/templates/includes/all-my-tasks.php"); ?>
		</div>
	</div>
</div>
{% endblock body %}
