<?php
require_once(home_dir . "apps/core/models.php");

$shown = array();

$projects = Project::mine($request->user);
if ($projects !== null) {
	$projects = $projects->order_by("owner");
	$orgs = array();
	foreach ($projects as $obj) {
		$owner = $obj->owner->__toString();
		if (!isset($orgs[$owner])) {
			print '<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" href="#owner'.$obj->owner->pk.'">
					<h4><i class="icon-check"></i> '.$owner.'</h4>
				</a>
			</div>
			<div id="owner'.$obj->owner->pk.'" class="accordion-body in collapse" style="height: auto; ">
				<div class="accordion-inner"><ul>';
		
			// Print all projects
			foreach (Project::mine($request->user)->find(array("owner" => $obj->owner)) as $project) {
				$shown[$project->pk] = true;
				print '<li><a href="'.home_url.'projects/'.$project->pk.'/">'.$project->name.'</a></li>';
			}
		
			print '</ul></div>
			</div>';
			$orgs[$owner] = true;
		}
	}
} else {
	print '<p>You dont currently have any projects!</p>';
}
?>
