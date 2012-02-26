<div class="modal" id="task-edit" style="max-height: 600px;">
  <div class="modal-header">
	 <a class="close" data-dismiss="modal">×</a>
	 <h3 class="task-title"></h3>
  </div>
  <div class="modal-body">
		<form action="" method="post" id="task-edit-form" class="form-horizontal">
	  		<fieldset>
				<input type="hidden" name="csrf" id="csrf-token" value="{{csrf_token}}" />
				<input type="hidden" name="pk" id="task-pk" value="" />
				<div class="control-group">
					<label class="control-label" for="task-name">Task Name</label>
			  		<div class="controls">
				  		<input name="name" id="task-name" />
				  	</div>
			  	</div>
				<div class="control-group">
					<label class="control-label" for="task-description">Task Description</label>
			  		<div class="controls">
				  		<textarea name="description" id="task-description"></textarea>
				  	</div>
			  	</div>
				<div class="control-group">
					<label class="control-label" for="task-progress">Task Progress</label>
			  		<div class="controls">
				  		<div class="input-append">
					  		<input name="progress" id="task-progress" class="span1" />
					  		<span class="add-on">%</span>
					  	</div>
					  	<label class="checkbox"><input type="checkbox" name="complete" id="task-complete" /> Complete</label>
				  	</div>
			  	</div>
				<div class="control-group">
					<label class="control-label" for="task-milestone">MileStone</label>
			  		<div class="controls">
					  	<select name="milestone" id="task-milestone">
					  		
					  	</select>
				  	</div>
			  	</div>
				<div class="control-group">
					<label class="control-label" for="task-type">Type</label>
			  		<div class="controls">
					  	<select name="type" id="task-type">
					  		<?php
					  			$dummy = new Task();
					  			$choices = $dummy->_type->get_choices();
					  			foreach ($choices as $num => $val) {
					  				print '<option value="'.$num.'">'.$val.'</option>';
					  			}
					  		?>
					  	</select>
				  	</div>
			  	</div>
				<div class="control-group">
					<label class="control-label" for="task-priority">Priority</label>
			  		<div class="controls">
					  	<select name="priority" id="task-priority">
					  		<?php
					  			$choices = $dummy->_priority->get_choices();
					  			foreach ($choices as $num => $val) {
					  				print '<option value="'.$num.'">'.$val.'</option>';
					  			}
					  		?>
					  	</select>
				  	</div>
			  	</div>
				<div class="control-group">
					<label class="control-label" for="task-status">Status</label>
			  		<div class="controls">
					  	<select name="status" id="task-status">
					  		<?php
					  			$choices = $dummy->_status->get_choices();
					  			foreach ($choices as $num => $val) {
					  				print '<option value="'.$num.'">'.$val.'</option>';
					  			}
					  		?>
					  	</select>
				  	</div>
			  	</div>
				<div class="control-group">
					<label class="control-label" for="task-assignees">Assignees</label>
			  		<div class="controls">
					  	<select name="assignees" id="task-assignees" multiple="multiple">
					  		<?php
					  			$choices = get_potential_assignees();
					  			foreach ($choices as $name => $arr) {
					  				if ($name !== "All Users")
					  					print '<option value="Team|'.$name.'">'.$arr.'</option>';
					  				else
					  					print '<option disabled="disabled">All Users</option>';
					  				foreach ($arr as $obj) {
					  					print '<option value="'.get_class($obj)."|".$obj->pk.'">&#160;&#160;&#160;&#160;&#160;'.$obj.'</option>';
					  				}
					  			}
					  		?>
					  	</select>
				  	</div>
			  	</div>
	  		</fieldset>
  		</form>
  </div>
  <div class="modal-footer">
	 <button class="btn btn-primary btn-save">Save</button>
	 <button class="btn" data-dismiss="modal">Close</button>
  </div>
</div>
