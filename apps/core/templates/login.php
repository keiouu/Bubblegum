{% extends "apps/core/templates/base.php" %}

{% block container %}
<div class="container">
	<div class="content">
		<div class="page-header">
			<h1>Login</h1>
		</div>
		<?php
		require_once(home_dir . "contrib/admin/core.php");
		$printer = new AdminFormPrinter();
		$printer->run($request->login_form, false, array("submit_login" => "login"));
		?>
		<p><a href="{{home_url}}register/"><i class="icon-user"></i> Dont have an account?</a></p>
	</div>
	<footer>
	  <p>© {{project_name}} 2012</p>
	</footer>
</div>
{% endblock container %}
