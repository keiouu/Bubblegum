{% extends "apps/core/templates/base.php" %}

{% block head %}
<link href="{{home_url}}apps/core/media/css/prettify.css" type="text/css" rel="stylesheet" />
{% endblock %}
    
{% block breadcrumbs %}
	<?php
	print '<li '.(isset($request->project) ? '' : 'class="active"').'><a href="'.home_url.'">Home</a></li>';
	print ' <li class="divider">/</li><li><a href="'.home_url.'projects/'.$request->project->pk.'/?tab=code">'.$request->project->name.'</a></li>';
	if (isset($args['ref'])) {
		print ' <li class="divider">/</li><li><a href="'.home_url.'projects/'.$request->project->pk.'/git/">git</a></li>';
		print ' <li class="divider">/</li><li class="active">'.$request->git_info['hash'].'</li>';
	} else {
		print ' <li class="divider">/</li><li class="active">git</li>';
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
	$git_file_changes = $git->file_changes($request->git_info['hash']);
	include_once(home_dir . "apps/core/templates/includes/git-files-changed.php");
	?>
{% endblock body %}

{% block endbody %}
<script src="{{home_url}}apps/core/media/js/code.js"></script>
<script type="text/javascript" src="{{home_url}}apps/core/media/js/prettify/prettify.js"></script>
{% endblock endbody %}
