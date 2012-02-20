{% extends "contrib/admin/templates/base.php" %}

{% block sidebar_menu %}
<div class="well">
  <h5>{% i18n "admin_menu" %}</h5>
  <p>{% i18n "admin_nothing" %}</p>
</div>
{% endblock %}
            
{% block body %}
<div class="main-unit">
	<?php
	require_once(home_dir . "contrib/admin/core.php");
	$printer = new AdminFormPrinter();
	$printer->run($request->modelform);
	?>
</div>
{% endblock %}
