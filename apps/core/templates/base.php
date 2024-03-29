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
    <meta name="author" content="James Thompson">
    
    {% block style %}{% endblock %}
	{% jsvars %}
    {% block head %}{% endblock %}
    
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    
    <link rel="shortcut icon" href="{{home_url}}favicon.ico">
  </head>

  <body>
	<div class="messages">
		<?php
			$array = $request->get_messages();
			foreach ($array as $type => $messages) {
				foreach ($messages as $message) {
					print '<div class="alert fade in alert-'.$type.'"><a class="close" data-dismiss="alert">×</a>' . $message . '</div>';
				}
			}
			$request->delete_messages();
		?>
	</div>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid top-row">
          <a class="brand" href="{{home_url}}"><?php if (defined("site_logo")) { print '<img src="'.site_logo.'" alt="logo" />'; } ?> {{project_name}}</a>
          <div class="nav-collapse">
            <ul class="nav">
              {% block menu %}
              <li <?php print (isset($request->project) || isset($request->team) ? '' : 'class="active"'); ?>><a href="{{home_url}}">Home</a></li>
              <?php if ($request->user->logged_in()) { ?>
              	<li class="dropdown<?php print (isset($request->project) ? ' active' : ''); ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Projects <b class="caret"></b></a>
		           <ul class="dropdown-menu">
		           	 <?php
		           	 $org = "";
		           	 $projects = Project::mine($request->user);
		           	 if ($projects !== null) {
				        	 foreach ($projects->order_by("owner") as $obj) {
				        	 	$owner = $obj->_owner->__toString();
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
				<li class="dropdown<?php print (isset($request->team) ? ' active' : ''); ?>"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Teams <b class="caret"></b></a>
				   <ul class="dropdown-menu">
					 <?php
					 $teams = Team::mine($request->user);
			    	 foreach ($teams as $team) {
			    	 	print '<li><a href="{{home_url}}teams/'.$team->pk.'/">'.$team->name.'</a></li>';
			    	 }
					 ?>
					 <li class="divider"></li>
					 <li><a href="{{home_url}}teams/new/"><i class="icon-plus"></i> Add New Team</a></li>
				   </ul>
				</li>
              <?php } ?>
              {% endblock %}
            </ul>
            <div class="pull-right">
					<ul class="nav">
						<li class="divider-vertical"></li>
				         <?php if ($request->user->logged_in())  { ?>
				         <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-wrench icon-white"></i></a>
				         	<ul class="dropdown-menu">
				         		<li class="nav-header">Themes</li>
				         		<?php
				         		$currentTheme = User_Preference::get_or_ignore(array("user" => $request->user));
								if (!$currentTheme) {
				         			$currentTheme = "standard";
								} else {
									$currentTheme = $currentTheme->value;
								}
								
				         		$themes = array("Standard", "Borg", "United", "Cerulean", "Simplex");
								foreach($themes as $theme) {
									$check = "";
									if ($currentTheme == strtolower($theme)) {
										$check = '<i class="icon-ok"></i> ';
									}
									print '<li><a href="'.$request->fullPath.'?theme='.strtolower($theme).'&csrf='.$request->get_csrf_token().'">'.$check . $theme.'</a></li>';
								}
				         		?>
				         		<li class="nav-header">Other</li>
				         		<li><a href="{{logout_url}}">Logout</a></li>
				         	</ul>
				         </li>
						 <?php } ?>
						<li class="divider-vertical"></li>
						<li><a href="{{home_url}}support/?referrer=<?php print htmlentities($request->fullPath); ?>" class="supportLink"><i class="icon-question-sign icon-white"></i></a></li>
		         </ul>
				</div>
            <div class="pull-right">
		         <p class="navbar-text">
		         <?php
		         if ($request->user->logged_in())  {
		         	print '<a class="nav-gravatar" href="{{home_url}}profile/'.$request->user->pk.'/" title="Logged in as '.$request->user->get_short_display_name().'"><img src="'.$request->gravatar . '?s=26" alt="Gravatar Image" class="gravatar" /></a>';
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
            <ul class="nav nav-list" id="activity_feed">
              <li class="nav-header"><i class="icon-fire"></i> Activity Feed</li>
              <li>Loading...</li>
            </ul>
          </div><!--/.well -->
			 {% endblock sidebar %}
        </div><!--/span-->
        <div class="span9">
				<ul class="breadcrumb">
				{% block breadcrumbs %}
					<?php
					print '<li '.(isset($request->project) ? '' : 'class="active"').'><a href="'.home_url.'">Home</a></li>';
					if (isset($request->project))
						print ' <li class="divider">/</li><li class="active">'.$request->project->name.'</li>';
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
    
    <media_manager type="JS" />
    {% block endbody %}
    {% endblock endbody %}
  </body>
</html>

