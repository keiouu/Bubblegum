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
        <div class="container-fluid top-row">
          <a class="brand" href="{{home_url}}"><?php if (defined("site_logo")) { print '<img src="'.site_logo.'" alt="logo" />'; } ?> {{project_name}}</a>
          <div class="nav-collapse">
            <ul class="nav">
              {% block menu %}
              <li <?php print (isset($request->project) ? '' : 'class="active"'); ?>><a href="{{home_url}}">Home</a></li>
              <?php if ($request->user->logged_in()) { ?>
              <li class="divider-vertical"></li>
              <li class="dropdown<?php print (isset($request->project) ? ' active' : ''); ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Projects <b class="caret"></b></a>
		           <ul class="dropdown-menu">
		           	 <?php
		           	 $org = "";
		           	 $projects = Project::mine($request->user);
		           	 if ($projects !== null) {
				        	 foreach ($projects->order_by("owner") as $obj) {
				        	 	$owner = $obj->owner->__toString();
				        	 	if ($org != $owner) {
				        	 		$org = $owner;
				        	 		print '<li class="nav-header">'.$org.'</li>';
				        	 	}
				        	 	print '<li><a href="{{home_url}}projects/'.$obj->pk.'/">'.$obj->name.'</a></li>';
				        	 }
				        	 print '<li class="divider"></li>';
		           	 }
		           	 ?>
		          	 <li><a href="{{home_url}}projects/new/"><i class="icon-plus"></i> Add New Project</a></li>
		           </ul>
		        </li>
              <?php } ?>
              {% endblock %}
            </ul>
            <div class="pull-right">
					<ul class="nav">
						<li class="divider-vertical"></li>
						<li><a href="{{home_url}}support/?referrer=<?php print htmlentities($request->fullPath); ?>" class="supportLink"><i class="icon-question-sign icon-white"></i></a></li>
		         </ul>
				</div>
            <div class="pull-right">
		         <p class="navbar-text">
		         <?php
		         if ($request->user->logged_in())  {
		         	print 'Logged in as <a href="{{home_url}}profile/'.$request->user->pk.'/">'.$request->user.'</a>';
		         } else {
		         	print '<a href="{{home_url}}login/">Log in</a>';
		         }
		         ?>
		         </p>
				</div>
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
    <script src="{{home_url}}apps/core/media/js/base.js"></script>
    <script src="{{home_url}}apps/core/media/js/activity.feed.js"></script>
    {% block endbody %}
    {% endblock endbody %}
  </body>
</html>

