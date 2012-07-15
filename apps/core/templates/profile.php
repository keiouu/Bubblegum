{% extends "apps/core/templates/base.php" %}

{% block breadcrumbs %}
	<?php
	print '<li><a href="'.home_url.'">Home</a></li>';
	print ' <li class="divider">/</li><li class="active">Profile</li>';
	?>
{% endblock %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1><img src="<?php print $request->gravatar . "?s=32"; ?>" alt="Gravatar Image" class="gravatar" /> <?php print $request->profile; ?></h1>
	</div>
	<p class="add-links">
		<a href="{{logout_url}}">Logout &raquo;</a>
	</p>
</div>
{% endblock body %}

