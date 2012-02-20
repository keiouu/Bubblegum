{% extends "contrib/admin/templates/base.php" %}

{% block breadcrumbs %}
<li><a href="{{home_url}}admin/">{% i18n "admin_home" %}</a> <span class="divider">/</span></li>
<li class="active">{% i18n "admin_upgrade" %}</li>
{% endblock %}

{% block body %}
<div class="main-unit">
	<?php
	require_once(home_dir . "framework/views/upgrade.php");
	$uview = new UpgradeView("/admin/upgrade/base/");
	$uview->render($request);
	?>
</div>
{% endblock %}
