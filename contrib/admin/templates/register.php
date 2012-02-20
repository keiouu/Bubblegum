{% extends "contrib/admin/templates/base.php" %}

{% block head %}
<link href="{{admin_media_url}}css/auth.css" rel="stylesheet">
{% endblock %}

{% block menu %}
<ul class="nav secondary-nav">
	<li class="dropdown"><a href="{{home_url}}admin/login/">{% i18n "admin_login" %}</a></li>
</ul>
{% endblock %}

{% block container %}
<div class="container">
	<div class="content">
		<div class="page-header">
			<h1>{% i18n "admin_register" %}</h1>
		</div>
		<?php
		require_once(home_dir . "contrib/admin/core.php");
		$printer = new AdminFormPrinter();
		$printer->run($request->admin_register_form, false, array("submit_register" => "register"));
		?>
	</div>
	<footer>
	  <p>{% i18n "admin_copyright" %}</p>
	</footer>
</div>
{% endblock container %}
