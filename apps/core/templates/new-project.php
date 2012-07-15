{% extends "apps/core/templates/base.php" %}

{% block breadcrumbs %}
	<?php
	print '<li><a href="'.home_url.'">Home</a></li>';
	print ' <li class="divider">/</li><li class="active">Create a project</li>';
	?>
{% endblock %}

{% block body %}
<div class="row-fluid">
	<?php
	$request->project_form->display(new BootstrapFormPrinter());
	?>
</div>
{% endblock body %}

