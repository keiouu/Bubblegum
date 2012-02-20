<div id="delete-confirm-modal" class="modal hide fade">
	<div class="modal-header">
	  <a href="#" class="close">&times;</a>
	  <h3>{% i18n "admin_confirm" %}</h3>
	</div>
	<div class="modal-body">
	  <p>{% i18n "admin_delete_confirm2" %}</p>
	</div>
	<div class="modal-footer">
	  <a href="#" class="btn primary" id="do-delete">{% i18n "admin_yes" %}</a>
	  <a href="#" class="btn secondary" id="dont-delete">{% i18n "admin_no" %}</a>
	</div>
</div>
<script type="text/javascript">
$(function () {	
	$("#dont-delete").click(function() {
		$("#delete-confirm-modal").modal('hide');
		return false;
	});
});
</script>
