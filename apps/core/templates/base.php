<?php
require_once(home_dir . "apps/core/models.php");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{title}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="{{home_url}}apps/core/media/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>
    <link href="{{home_url}}apps/core/media/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="{{home_url}}apps/core/media/css/style.css" rel="stylesheet">
	 {% jsvars %}
	 
    {% block head %}
    {% endblock %}
    
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    
    <link rel="shortcut icon" href="{{home_url}}favicon.ico">
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="{{home_url}}"><?php if (defined("site_logo")) { print '<img src="'.site_logo.'" alt="logo" />'; } ?> {{project_name}}</a>
          <div class="nav-collapse">
            <ul class="nav">
              {% block menu %}
              <li <?php print (isset($request->project) ? '' : 'class="active"'); ?>><a href="{{home_url}}">Home</a></li>
              <li class="divider-vertical"></li>
              <li class="dropdown<?php print (isset($request->project) ? ' active' : ''); ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Projects <b class="caret"></b></a>
		           <ul class="dropdown-menu">
		            <li class="nav-header">Select a Project</li>
		           	 <?php
		           	 foreach (Project::objects()->all() as $obj) {
		           	 	print '<li><a href="{{home_url}}projects/'.$obj->pk.'/">'.$obj->name.'</a></li>';
		           	 }
		           	 ?>
		           	 <li class="divider"></li>
		          	 <li><a href="{{home_url}}projects/new/"><i class="icon-plus"></i> Add New Project</a></li>
		           </ul>
		        </li>
              {% endblock %}
            </ul>
            <?php
            if ($request->user->logged_in())  {
            	print '<p class="navbar-text pull-right">Logged in as <a href="{{home_url}}profile/'.$request->user->pk.'/">'.$request->user.'</a></p>';
            } else {
            	print '<p class="navbar-text pull-right"><a href="{{home_url}}login/">Log in</a></p>';
            }
            ?>
            
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    {% block container %}
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span2">
			 {% block sidebar %}
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header"><i class="icon-fire"></i> Activity Feed</li>
              <span id="activity_feed"><li>Loading...</li></span>
            </ul>
          </div><!--/.well -->
			 {% endblock sidebar %}
        </div><!--/span-->
        <div class="span9">
				<ul class="breadcrumb">
				{% block breadcrumbs %}
					<?php
					print '<li '.(isset($request->project) ? '' : 'class="active"').'><a href="'.home_url.'">{% i18n "admin_home" %}</a></li>';
					if (isset($request->project))
						print ' <span class="divider">/</span><li class="active">'.$request->project->name.'</li>';
					?>
				{% endblock %}
			 </ul>
          {% block body %}
          {% endblock body %}
        </div><!--/span-->
      </div><!--/row-->

      <hr>

      <footer>
        <p>&copy; Tikapot.com 2012</p>
      </footer>

    </div><!--/.fluid-container-->
    {% endblock container %}
    
    <script src="{{home_url}}apps/core/media/js/jquery.min.js"></script>
    <script src="{{home_url}}apps/core/media/js/bootstrap.min.js"></script>
    <script src="{{home_url}}apps/core/media/js/tikapot.ajax.js"></script>
    <script src="{{home_url}}apps/core/media/js/activity.feed.js"></script>
    {% block endbody %}
    {% endblock endbody %}
  </body>
</html>

