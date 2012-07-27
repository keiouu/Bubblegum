{% extends "apps/core/templates/base.php" %}

{% block breadcrumbs %}
	<?php
	print '<li><a href="'.home_url.'">Home</a></li>';
	print ' <li class="divider">/</li><li class="active">Team</li>';
	?>
{% endblock %}

{% block body %}
<div class="row-fluid">
<?php
if (!$request->team) {
	print '<h1>Team not found! :(</h1>';
} else {
	print $request->team->name;
}
?>
</div>
{% endblock body %}
