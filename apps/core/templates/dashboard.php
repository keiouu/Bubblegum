{% extends "apps/core/templates/base.php" %}

{% block style %}
<link href="{{home_url}}apps/core/media/css/dashboard.css" rel="stylesheet">
{% endblock %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1>Dashboard</h1>
	</div>
      
      <div style="height: 25px;width: 100%;"></div>
      
      <div class="span3 draggables c1">
      	<h3>Product Backlog</h3>
      	<div class="inner">
	      	<div class="draggable btn btn-primary">
		      	<p class="title">Do this and this and this</p>
		      	<p class="author">James Thompson</p>
	      	</div>
	      	<div class="draggable btn btn-primary">
		      	<p class="title">Task1</p>
		      	<p class="author">James Thompson</p>
	      	</div>
      	</div>
      </div>

      <div class="span3 draggables c1">
      	<h3>Sprint Backlog</h3>
      	<div class="inner">
	      	<div class="draggable btn btn-danger">
		      	<p class="title">Task2</p>
		      	<p class="author">James Thompson</p>
	      	</div>
	      	<div class="draggable btn btn-primary">
		      	<p class="title">Task3</p>
		      	<p class="author">James Thompson</p>
	      	</div>
      	</div>
      </div>
      
      <div class="span3 draggables c1">
      	<h3>Complete</h3>
      	<div class="inner">
	      	<div class="draggable btn btn-warning">
		      	<p class="title">Task4</p>
		      	<p class="author">James Thompson</p>
	      	</div>
	      	<div class="draggable btn btn-success">
		      	<p class="title">Task5</p>
		      	<p class="author">James Thompson</p>
	      	</div>
	      	<div class="draggable btn btn-success">
		      	<p class="title">Task 6 - Do blah blah blah blah blah blah blah blah blah blah blah blah blah blah blah</p>
		      	<p class="author">James Thompson</p>
	      	</div>
      	</div>
      </div>
    
</div>
{% endblock body %}

{% block endbody %}
<script src="{{home_url}}apps/core/media/js/utils.js"></script>
<script src="{{home_url}}apps/core/media/js/project.feeds.js"></script>
<script src="{{home_url}}apps/core/media/js/feeds.ajax.js"></script>
<script src="{{home_url}}apps/core/media/js/jquery.drag.js"></script>
<script src="{{home_url}}apps/core/media/js/jquery.utils.js"></script>
<script src="{{home_url}}apps/core/media/js/dashboard.js"></script>
{% endblock endbody %}

