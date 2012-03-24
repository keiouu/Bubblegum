{% extends "apps/core/templates/base.php" %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1>Your Projects..</h1>
	</div>
	<?php include_once(home_dir . "apps/core/templates/includes/my-projects.php"); ?>
</div>

<?php
print '<div class="row-fluid well"><h3>Get involved in some other projects..</h3><br />';

// Show some random public projects to "get involved in"
$count = 0;
$projects = Project::find(array("public" => true))->shuffle();
foreach ($projects as $project) {
	if (isset($shown[$project->pk]))
		continue;
	if ($count >= 5)
		break;
	print '<div class="span2">
		<h4>'.$project->name.'</h4>
		<p>'.$project->description.'</p>
		<p><a class="btn" href="'.home_url.'projects/'.$project->pk.'/">Get Involved &raquo;</a></p>
	</div>';
	$count++;
	$shown[$project->pk] = true;
}

print '</div>';
?>
{% endblock body %}

