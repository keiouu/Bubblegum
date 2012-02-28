{% extends "apps/core/templates/base.php" %}

{% block body %}
<div class="row-fluid">
	<div class="page-header">
		<h1><?php print $request->profile; ?></h1>
	</div>
</div>
{% endblock body %}

