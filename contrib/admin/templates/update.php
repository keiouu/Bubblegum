{% extends "contrib/admin/templates/base.php" %}

{% block breadcrumbs %}
<li><a href="{{home_url}}admin/">{% local_i18n "admin_home" %}</a> <span class="divider">/</span></li>
<li class="active">{% local_i18n "admin_update" %}</li>
{% endblock %}

{% block body %}
<div class="hero-unit">
	<h2>{% local_i18n "admin_update" %}</h2>
	<?php
	// TODO - This will all be done through AJAX
	// TODO - Ensure we have write access and curl
	require_once(home_dir . "contrib/admin/update.php");
	require_once(home_dir . "framework/config_manager.php");
	require_once(home_dir . "framework/utils.php");
	
	// Check for the latest version of the framework
	print '<br />' . $request->i18n['admin']['admin_check_framework'];
	if (TPUpdater::needs_update("framework")) {
		$version = TPUpdater::get_remote_version("framework");
		print '  ' . str_replace("#version#", $version, $request->i18n['admin']['admin_check_version']);
		print '<div class="update_entry">' . $request->i18n['admin']['admin_update_framework'];
		TPUpdater::update("framework");
		print '</div>';
	}
	
	// Check for the latest version of the apps
	$apps = ConfigManager::get_app_list();
	foreach ($apps as $app) {
		if (!starts_with($app, "contrib"))
			continue;
		print '<br />';
		$app_name = substr($app, strrpos($app, '/') + 1);
		print str_replace("#app#", $app_name, $request->i18n['admin']['admin_check_app']);
		if (TPUpdater::needs_update($app_name)) {
			$version = TPUpdater::get_remote_version($app_name);
			print '  ' . str_replace("#version#", $version, $request->i18n['admin']['admin_check_version']);
			print '<div class="update_entry">' . str_replace("#app#", $app_name, $request->i18n['admin']['admin_update_app']);
			TPUpdater::update($app_name);
			print '</div>';
		}
	}
	?>
</div>
{% endblock %}
