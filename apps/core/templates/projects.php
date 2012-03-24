{% extends "apps/core/templates/base.php" %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1>Your Projects..</h1>
	</div>
	<?php include_once(home_dir . "apps/core/templates/includes/my-projects.php"); ?>
</div>
{% endblock body %}

