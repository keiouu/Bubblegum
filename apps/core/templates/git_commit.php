{% extends "apps/core/templates/base.php" %}

{% block breadcrumbs %}
	<?php
	print '<li '.(isset($request->project) ? '' : 'class="active"').'><a href="'.home_url.'">Home</a></li>';
	print ' <span class="divider">/</span><li><a href="'.home_url.'projects/'.$request->project->pk.'/">'.$request->project->name.'</a></li>';
	print ' <span class="divider">/</span><a href="'.home_url.'projects/'.$request->project->pk.'/git/">git</a></li>';
	print ' <span class="divider">/</span><li class="active">'.$request->git_info['hash'].'</li>';
	?>
{% endblock %}

{% block body %}
	<?php
	$git = $request->project->getRepository();
	$git_data = $git->log($args['ref']);
	$show_ref = false;
	include_once(home_dir . "apps/core/templates/includes/git-view.php");
	?>
{% endblock body %}


