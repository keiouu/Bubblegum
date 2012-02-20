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
    {% block head %}
    {% endblock %}
    
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
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
          <a class="brand" href="{{home_url}}">TNet</a>
          <div class="nav-collapse">
            <ul class="nav">
              {% block menu %}
              <li class="active"><a href="{{home_url}}">Home</a></li>
              <li class="divider-vertical"></li>
              <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Projects <b class="caret"></b></a>
		           <ul class="dropdown-menu">
		            <li class="nav-header">Select a Project</li>
		           	 <?php
		           	 foreach (Project::objects()->all() as $obj) {
		           	 	print '<li><a href="{{home_url}}projects/'.$obj->pk.'/">'.$obj->name.'</a></li>';
		           	 }
		           	 ?>
		           	 <li class="divider"></li>
		          	 <li><a href="{{home_url}}projects/new/">Add New Project</a></li>
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
              <li class="nav-header">Activity Feed</li>
              <li><a href="{{home_url}}">Something happened!</a></li>
            </ul>
          </div><!--/.well -->
			 {% endblock sidebar %}
        </div><!--/span-->
        <div class="span9">
				<ul class="breadcrumb">
				{% block breadcrumbs %}
					<?php
					if (isset($request->app)) {
						print '<li><a href="'.home_url.'">{% i18n "admin_home" %}</a> <span class="divider">/</span></li>';
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
						print '<li '.(isset($request->app_name) ? '' : 'class="active"').'><a href="'.home_url.'">{% i18n "admin_home" %}</a></li>';
						if (isset($request->app_name))
							print ' <span class="divider">/</span><li class="active">'.$request->app_name.'</li>';
					}
					?>
				{% endblock %}
			 </ul>
          {% block body %}
          <div class="hero-unit">
            <h1>Hello, world!</h1>
            <p>This is a template for a simple marketing or informational website. It includes a large callout called the hero unit and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
            <p><a class="btn btn-primary btn-large">Learn more &raquo;</a></p>
          </div>
          <div class="row-fluid">
            <div class="span4">
              <h2>Heading</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
            <div class="span4">
              <h2>Heading</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
            <div class="span4">
              <h2>Heading</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
          </div><!--/row-->
          <div class="row-fluid">
            <div class="span4">
              <h2>Heading</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
            <div class="span4">
              <h2>Heading</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
            <div class="span4">
              <h2>Heading</h2>
              <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
              <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
          </div><!--/row-->
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
  </body>
</html>

