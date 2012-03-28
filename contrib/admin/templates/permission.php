{% extends "contrib/admin/templates/base.php" %}

{% block sidebar %}
&nbsp;
{% endblock sidebar %}

{% block breadcrumb_container %}
&nbsp;
{% endblock %}

{% block body %}
<div class="hero-unit">
	<h2>{% local_i18n "admin_permission_failure" %}</h2>
</div>
{% endblock %}
