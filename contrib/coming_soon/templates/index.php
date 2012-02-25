{% set_app "coming_soon" %}
<!DOCTYPE html> 
<html lang="en">
<head> 
  <meta charset="utf-8">
 
  <title>{{title}}</title> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Tikapot"> 
  <meta name="author" content="Tikapot">
  
  <link href='http://fonts.googleapis.com/css?family=Lustria' rel='stylesheet' type='text/css'>
  <link href="{{home_url}}contrib/coming_soon/media/reset.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="{{home_url}}contrib/coming_soon/media/text.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="{{home_url}}contrib/coming_soon/media/style.css" rel="stylesheet" type="text/css" media="screen" />
</head> 
 
<body>
	<div id="particles"></div>
	<div id="content">
		<h1>{% local_i18n "title" %}</h1>
		<div class="timeroller">
			<div class="pair">
				<div id="days">{{days}}</div><div>{% local_i18n "days" %}</div>
			</div>
			<div class="pair">
				<div id="hours">{{hours}}</div><div>{% local_i18n "hours" %}</div>
			</div>
			<div class="pair">
				<div id="minutes">{{minutes}}</div><div>{% local_i18n "minutes" %}</div>
			</div>
			<div class="pair">
				<div id="seconds">{{seconds}}</div><div>{% local_i18n "seconds" %}</div>
			</div>
		</div>
		<p>This site is currently being built, and will be with you shortly.<br />
		While you're waiting why not check out <a href="http://www.tikapot.com">Tikapot</a>?</p>
	</div>
	<script type="text/javascript" src="{{home_url}}contrib/coming_soon/media/jquery.js"></script>
	<script type="text/javascript" src="{{home_url}}contrib/coming_soon/media/counter.js"></script>
	<script type="text/javascript" src="{{home_url}}contrib/coming_soon/media/fun.js"></script>
</body> 
</html> 
