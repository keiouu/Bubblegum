<?php require_once(home_dir . "apps/core/forms.php"); ?>
<div class="modal" id="milestone-add">
  <div class="modal-header">
	 <a class="close" data-dismiss="modal">×</a>
	 <h3>Add a new Milestone</h3>
  </div>
  <div class="modal-body">
	 <?php
	 $form = new MileStoneAddForm();
	 $form->display();
	 ?>
  </div>
  <div class="modal-footer">
	 <a href="#" class="btn btn-primary btn-save">Save</a>
	 <a href="#" class="btn" data-dismiss="modal">Close</a>
  </div>
</div>
