{% extends "contrib/admin/templates/base.php" %}

{% block sidebar_menu %}
{% endblock %}
            
{% block body %}
<div class="hero-unit">
	<h2><?php print $request->app_name; ?></h2>
	<ul>
		<?php
		foreach ($request->apps[$request->app_name] as $app_name => $url) {
			print '<li><a href="'.$url.'">'.$app_name.'</a></li>';
		}
		?>
	</ul>
</div>
{% endblock %}
