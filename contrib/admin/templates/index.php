{% extends "contrib/admin/templates/base.php" %}

{% block body %}
<div class="hero-unit">
	<h2>{% i18n "admin_welcome" %}</h2>
	<p>{% i18n "admin_welcome_desc" %}</p>
</div>
<?php
require_once(home_dir . "contrib/admin/core.php");

$objects = AdminManager::get_panels();
if (count($objects) > 0) {
	print '<div id="index-panels">';
	foreach ($objects as $object) {
		$data = $object->render($request);
		if (strlen($data) > 0) {
			print '	<div class="well index-panel">
							<h5>'.$object->get_title().'</h5>
							'.$data.'
					 	</div>';
		}
	}
	print '</div>';
}
?>
<script type="text/javascript">
$(function () {
	$("#index-panels").sortable({
		revert: true,
	});
});
</script>
{% endblock %}
