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
	print '
	<div class="page-header">
		<h1>'.$request->team->name.' <a href="#" id="track" rel="popover" title="'.$request->team->name.'" data-content="'.$request->team->description.'"><i class="icon-question-sign"></i></a></h1>
	</div>
	
	<h2>Projects</h2>
	<ul class="nav nav-tabs nav-stacked">';
	$projects = $request->team->projects();
	if ($projects->count() > 0) {
		foreach ($projects as $project) {
			print '<li><a href="'.home_url.'projects/'.$project->pk.'/">'.$project->name.'</a></li>';
		}
	} else {
		print '<li>None!</li>';
	}
	
	print '</ul>';
}
?>
</div>
{% endblock body %}
