{% extends "contrib/admin/templates/base.php" %}

{% block sidebar_menu %}
<div class="well">
  <h5>{% i18n "admin_menu" %}</h5>
  <?php
  // Extra actions
  foreach ($request->admin->get_actions() as $action) {
	print $action->render($request, $request->model_obj);
  }
  ?>
  <button class="btn btn-danger" id="button_delete" data-toggle="modal" data-target="#delete-confirm-modal" data-backdrop="static">{% i18n "admin_delete" %}</button>
</div>
{% endblock %}
            
{% block body %}
<div class="main-unit">
	<?php
	require_once(home_dir . "contrib/admin/core.php");
	$printer = new AdminFormPrinter();
	$printer->run($request->modelform, true);
	?>
</div>
{% include "contrib/admin/templates/includes/delete-modal.php" %}
<script type="text/javascript">
$(function () {	
	$("#do-delete").click(function() {
		$(this).attr("href", "<?php echo $this->model_url . "?delete=" . $request->model_obj->pk;?>");
	});
});
</script>
{% endblock %}
