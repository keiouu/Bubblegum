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
	require_once(home_dir . "framework/models.php");
	function auto_discover($object) {
		$ret = '<li>'.get_class($object).': '.$object;
		foreach (ContentType::objects()->all() as $ct) {
			$obj = $ct->obtain();
			if ($obj) {
				$string = '';
				foreach ($obj->get_related_objects($object) as $relation) {
					$string .= auto_discover($relation);
				}
				if ($string !== '') {
					$ret .= '<ul>' . $string . '</ul>';
				}
			}
		}
		return $ret . '</li>';
	}
	
	$ids = explode(",", $request->get['delete']);
	foreach ($ids as $id) {
		$object = $this->model->get_or_ignore(array("pk" => $id));
		print auto_discover($object);
	}
	?>
	</ul>
	<a href="<?php print $request->fullPath . '?' . $request->query_string() . '&confirm=true'; ?>" class="btn btn-success">I'm Sure</a>
	<a href="<?php print $request->fullPath; ?>" class="btn btn-danger">No</a>
</div>
{% endblock %}
