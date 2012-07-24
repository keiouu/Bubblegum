<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{title}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	{% block meta %}
    <meta name="description" content="">
    <meta name="author" content="">
	{% endblock meta %}
    
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    
    <link rel="shortcut icon" href="{{media_url}}favicon.ico">
    <link type="text/plain" rel="author" href="{{home_url}}humans.txt" />
  </head>

  <body>
	{% block body-top %}
	{% endblock body-top %}

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand<?php print defined("site_logo") ? ' logo-pad' : ''; ?>" href="{{home_url}}">
	          <?php print defined("site_logo") ? '<img src="'.site_logo.'" alt="logo" class="logo" />' : ''; ?>
	          {{project_name}}
	      </a>
          <div class="nav-collapse">
			{% block menu %}
            <ul class="nav">
              {% block menu-items %}
              <li class="active"><a href="{{home_url}}">Home</a></li>
              {% endblock menu-items %}
            </ul>
            {% endblock menu %}
          </div>
        </div>
      </div>
    </div>

    <div class="container">
    	{% block container %}
    	{% endblock container %}
    </div>
	
	{% block body-bottom %}
	{% endblock body-bottom %}
  </body>
</html>
