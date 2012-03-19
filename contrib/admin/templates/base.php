<?php
require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "framework/signal_manager.php");
require_once(home_dir . "contrib/admin/core.php");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>{{title}}</title>
		<meta name="description" content="{% block meta_description %}Tikapot Administration panel{% endblock %}">
		<meta name="author" content="{{project_name}}">

		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<link href="{{admin_media_url}}css/jquery-ui.css" rel="stylesheet">
		<link href="{{admin_media_url}}css/bootstrap.min.css" rel="stylesheet">
		<link href="{{admin_media_url}}css/style.css" rel="stylesheet">
		<style type="text/css">
      body {
        padding-top: 60px;
      }
    	</style>
		{% block head %}{% endblock %}
		<script src="{{admin_media_url}}js/jquery.js"></script>
		<script src="{{admin_media_url}}js/jquery-ui.js"></script>
		<script src="{{admin_media_url}}js/jquery-ui-timepicker-addon.js"></script>
		<script src="{{admin_media_url}}js/bootstrap.min.js"></script>
		<script type="text/javascript">
		function show_messages() {
			$('.messages').show();
			$('.alert-message').show('blind', {}, 1000);
		}
		
		function hide_messages() {
			$('.alert-message').hide('blind', {}, 1000, function() {
				$('.messages').hide();
			});
		}
		
		$(function () {
			$('#topbar').dropdown();
			
			// Messages
			if ($('.alert-message').length > 0)
				show_messages();
			setTimeout("hide_messages();", 2000 * $('.alert-message').length);
			
			// Forms
			$(".datetimefield").datetimepicker({
				dateFormat: 'yy-mm-dd',
				timeFormat: 'hh:mm:00',
			});
			$(".datefield").datepicker({
				dateFormat: 'yy-mm-dd',
			});
		});
		</script>
	</head>

	<body>
		<div class="navbar navbar-fixed-top">
      	<div class="navbar-inner">
				<div class="container-fluid top-row">
					<a class="brand" href="{{home_url}}admin/"><?php if (defined("site_logo")) { print '<img src="'.site_logo.'" alt="logo" />'; } ?>{{project_name}}</a>
					{% block menu %}
					<ul class="nav">
						<?php
						ConfigManager::init_app_configs();
						$app_configs = ConfigManager::get_all_app_configs();
						print '<li class="'.(!isset($request->app) ? "active" : "").'"><a href="'.home_url.'admin/">Home</a></li>';
						foreach ($request->apps as $name => $app) {
							if (!$request->user->has_permission("admin_site_app_" . $name))
								continue;
							print '<li class="dropdown'.((isset($request->app) && $request->app == $name) ? ' active' : ' ').'" data-dropdown="dropdown">';
							?>
	         			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo prettify($name); ?><b class="caret"></b></a>
	         			<ul class="dropdown-menu">
					   		<?php
					   		$count = 0;
					   		foreach ($app as $app_name => $url) {
									if (!$request->user->has_permission("admin_site_model_" . $app_name))
										continue;
					   			print '<li><a href="' . $url . '">' . $app_name . '</a></li>';
					   			$count++;
					  			}
					  			if ($count == 0)
					   			print '<li><a href="#">{% i18n "admin_no_apps" %}</a></li>';
								
								// App cache
								if ($request->user->has_permission("admin_site_app_" . $name . "_config")) {
									if (isset($app_configs[$name]) && count($app_configs[$name]) > 0) {
										print '<li class="divider"></li>';
					   				print '<li><a href="{{home_url}}admin/core/App_Config/?_app='.$name.'">{% i18n "admin_app_config" %}</a></li>';
										unset($app_configs[$name]);
									}
								}
					  			?>
	           			</ul>
         			</li>
         			<?php
         			}
         			foreach($app_configs as $cfg_app => $cfgs) {
							if (!$request->user->has_permission("admin_site_model_" . $cfg_app . "_config"))
								continue;
							print '<li class="dropdown" data-dropdown="dropdown">
								<a href="#" class="dropdown-toggle">'.prettify($cfg_app).'</a>
	         				<ul class="dropdown-menu">
	         					<li><a href="{{home_url}}admin/core/App_Config/?_app='.$cfg_app.'">{% i18n "admin_app_config" %}</a></li>
	         				</ul>
	         			</li>';
         			}
         			?>
					</ul>
					<?php if($request->user->logged_in()) { ?>
					<div class="pull-right">
						<ul class="nav">
							<li class="divider-vertical"></li>
							<li><a href="{{logout_url}}">{% i18n "admin_logout" %}</a></li>
						</ul>
					</div>
					<div class="pull-right">
						<p class="navbar-text">
							{% i18n "admin_welcome2" %}&nbsp;{% date "H:ia" %}&nbsp;{% date "jS, M Y" %}
						</p>
					</div>
					<?php } else { ?>
					<div class="pull-right">
						<ul class="nav">
							<li><a href="{{home_url}}admin/login/">{% i18n "admin_login" %}</a></li>
							<li class="divider-vertical"></li>
							<li><a href="{{home_url}}admin/register/">{% i18n "admin_register" %}</a></li>
						</ul>
					</div>
					<?php } ?>
					{% endblock %}
        		</div>
      	</div>
		</div>
		<div class="messages">
			<?php
				foreach ($request->get_messages() as $type => $messages) {
					foreach ($messages as $message) {
						print '<div class="alert-message fade in '.$type.'" data-alert="true"><a class="close" href="#">×</a><p>' . $message . '</p></div>';
					}
				}
				$request->delete_messages();
			?>
		</div>
		{% block container %}
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span2">
            
				{% block sidebar %}
				<?php SignalManager::fire("admin_pre_sidebar", $request); ?>
				{% block sidebar_menu %}
				<?php if ($request->user->has_permission("tikapot_config_view") || $request->user->has_permission("tikapot_upgrade") || $request->user->has_permission("tikapot_update")) { ?>
					<div class="well sidebar-nav">
						<h5>{% i18n "admin_menu" %}</h5>
						<ul class="nav nav-list">
							<?php if ($request->user->has_permission("tikapot_config_view")) { ?>
							<li><a href="{{home_url}}admin/config/">{% i18n "admin_edit_config" %}</a></li>
							<?php } ?>
							<?php if ($request->user->has_permission("tikapot_upgrade")) { ?>
							<li><a href="{{home_url}}admin/upgrade/">{% i18n "admin_upgrade" %}</a></li>
							<?php } ?>
							<?php if ($request->user->has_permission("tikapot_update")) { ?>
							<li><a href="{{home_url}}admin/update/">{% i18n "admin_update" %}</a></li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>
				{% endblock %}
				<?php
				foreach (AdminManager::get_sidebars() as $object) {
					$data = $object->render($request);
					if (strlen($data) > 0) {
						print '	<div class="well sidebar-nav">
										<h5>'.$object->get_title().'</h5>
										'.$data.'
								 	</div>';
					}
				}
				SignalManager::fire("admin_post_sidebar", $request);
				?>
				{% endblock sidebar %}
		   	</div>
				<div class="span9">
					{% block breadcrumb_container %}
					<ul class="breadcrumb">
					{% block breadcrumbs %}
						<?php
						if (isset($request->app)) {
							print '<li><a href="'.home_url.'admin/">{% i18n "admin_home" %}</a> <span class="divider">/</span></li>';
							print '<li><a href="'.$request->app_url.'">'.$request->app.'</a> <span class="divider">/</span></li>';
							if (isset($request->admin_add)) {
								print '<li><a href="'.$request->model_url.'">'.$request->model.'</a> <span class="divider">/</span></li>';
								print '<li class="active"><a href="'.$request->fullPath.'">{% i18n "admin_add_new" %}</a></li>';
							} elseif (isset($request->admin_edit)) {
								print '<li><a href="'.$request->model_url.'">'.$request->model.'</a> <span class="divider">/</span></li>';
								print '<li class="active"><a href="'.$request->fullPath.'">{% i18n "admin_edit" %}</a></li>';
							} else {
								print '<li class="active"><a href="'.$request->fullPath.'">'.$request->model.'</a></li>';
							}
						} else {
							print '<li '.(isset($request->app_name) ? '' : 'class="active"').'><a href="'.home_url.'admin/">{% i18n "admin_home" %}</a></li>';
							if (isset($request->app_name))
								print ' <span class="divider">/</span><li class="active">'.$request->app_name.'</li>';
						}
						?>
					{% endblock %}
					</ul>
					{% endblock breadcrumb_container %}
					
					{% block body %}{% endblock %}
				</div>
			</div>
			<hr>
			<footer>
		  		<p>{% block footer %}{% i18n "admin_copyright" %}{% endblock %}</p>
	  		</footer>
		</div>
		{% endblock container %}
	</body>
</html>
