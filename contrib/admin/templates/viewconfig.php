{% extends "contrib/admin/templates/base.php" %}

{% block breadcrumbs %}
<li><a href="{{home_url}}admin/">{% i18n "admin_home" %}</a> <span class="divider">/</span></li>
<li class="active">{% i18n "admin_edit_config" %}</li>
{% endblock %}
            
{% block body %}
<div class="main-unit">
	<?php
	if (is_writable(home_dir . "config.php")) {
		print '<div class="alert-message warning fade in" data-alert="true">
        <a class="close" href="#">×</a>
        <p>{% i18n "admin_config_error1" %}</p>
      </div>';
	}
	?>
	<table class="bordered-table zebra-striped">
		<tbody>
			<?php
				print '<tr><td>page_def</td><td>'.page_def.'</td></tr>';
				print '<tr><td>project_name</td><td>'.project_name.'</td></tr>';
				print '<tr><td>home_dir</td><td>'.home_dir.'</td></tr>';
				print '<tr><td>home_url</td><td>'.home_url.'</td></tr>';
				print '<tr><td>media_dir</td><td>'.media_dir.'</td></tr>';
				print '<tr><td>media_url</td><td>'.media_url.'</td></tr>';
				print '<tr><td>font_dir</td><td>'.font_dir.'</td></tr>';
				print '<tr><td>debug</td><td>'.(debug ? "true" : "false").'</td></tr>';
				print '<tr><td>debug_show_queries</td><td>'.(debug_show_queries ? "true" : "false").'</td></tr>';
				
				global $tp_options, $app_paths, $apps_list;
				foreach ($tp_options as $key => $value) {
					print '<tr><td>'.$key.'</td><td>'.(is_bool($value) ? ($value ? "True" : "False") : $value).'</td></tr>';
				}
				print '<tr><td>app_paths</td><td>'.implode(", ", $app_paths).'</td></tr>';
				print '<tr><td>apps_list</td><td>'.implode(", ", $apps_list).'</td></tr>';
			?>
		</tbody>
	</table>
</div>
{% endblock %}
