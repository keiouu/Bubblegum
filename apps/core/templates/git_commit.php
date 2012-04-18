{% extends "apps/core/templates/base.php" %}

{% block breadcrumbs %}
	<?php
	print '<li '.(isset($request->project) ? '' : 'class="active"').'><a href="'.home_url.'">Home</a></li>';
	print ' <span class="divider">/</span><li><a href="'.home_url.'projects/'.$request->project->pk.'/">'.$request->project->name.'</a></li>';
	if (isset($args['ref'])) {
		print ' <span class="divider">/</span><li><a href="'.home_url.'projects/'.$request->project->pk.'/git/">git</a></li>';
		print ' <span class="divider">/</span><li class="active">'.$request->git_info['hash'].'</li>';
	} else {
		print ' <span class="divider">/</span><li class="active">git</li>';
	}
	?>
{% endblock %}

{% block body %}
	<?php
	$git = $request->project->getRepository();
	$show_ref = false;
	$git_data = $request->git_info;
	include_once(home_dir . "apps/core/templates/includes/git-view.php");
	$git_changes = $git->files_changed($request->git_info['hash']);
	include_once(home_dir . "apps/core/templates/includes/git-files-changed.php");
	?>
{% endblock body %}


