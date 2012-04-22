{% extends "apps/core/templates/base.php" %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1>Create a project..</h1>
		<?php
		$request->project_form->display();
		?>
	</div>
</div>
{% endblock body %}

