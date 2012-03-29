{% extends "contrib/admin/templates/base.php" %}

{% block sidebar_menu %}
<div class="well">
  <h5>{% local_i18n "admin_menu" %}</h5>
  <p>{% local_i18n "admin_nothing" %}</p>
</div>
{% endblock %}
            
{% block body %}
<div class="main-unit">
	<h3>{% local_i18n "delete1" %}</h3>
	<p>{% local_i18n "delete2" %}</p>
	<ul>
	<?php
	require_once(home_dir . "framework/database.php");
	function auto_discover($object) {
		print '<li>'.get_class($object).': '.$object;
		// TODO - check all objects that might link to this, and then their objects!
		print '</li>';
	}
	
	$ids = explode(",", $request->get['delete']);
	foreach ($ids as $id) {
		$object = $this->model->get_or_ignore(array("pk" => $id));
		auto_discover($object);
	}
	?>
	</ul>
</div>
{% endblock %}
