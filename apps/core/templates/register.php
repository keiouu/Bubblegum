{% extends "apps/core/templates/base.php" %}

{% block container %}
<div class="container">
	<div class="content">
		<div class="page-header">
			<h1>Register</h1>
		</div>
		<?php
		$printer = new BootstrapFormPrinter();
		$printer->run($request->register_form, false, array("submit_register" => "register"));
		?>
		<p><a href="{{home_url}}login/"><i class="icon-user"></i> Already have an account?</a></p>
	</div>
	<footer>
	  <p>© {{project_name}} 2012</p>
	</footer>
</div>
{% endblock container %}
