{% extends "contrib/admin/templates/base.php" %}

{% block head %}
<link href="{{admin_media_url}}css/auth.css" rel="stylesheet">
{% endblock %}

{% block container %}
<div class="container">
	<div class="content">
		<div class="page-header">
			<h1>{% local_i18n "admin_register" %}</h1>
		</div>
		<?php
		require_once(home_dir . "contrib/admin/core.php");
		$printer = new AdminFormPrinter();
		$printer->run($request->admin_register_form, false, array("submit_register" => "register"));
		?>
	</div>
	<footer>
	  <p>{% local_i18n "admin_copyright" %}</p>
	</footer>
</div>
{% endblock container %}
