<div class="modal" id="deployments-add" style="max-height: 600px;">
  <div class="modal-header">
	 <a class="close" data-dismiss="modal">×</a>
	 <h3>Add Deployment</h3>
  </div>
  <div class="modal-body">
		<form action="" method="post" id="deployment-add-form" class="form-horizontal">
	  		<fieldset>
				<input type="hidden" name="csrf" id="add-deployment-csrf-token" value="{{csrf_token}}" />
				<input type="hidden" name="pk" id="add-deployment-pk" value="" />
				<div class="control-group">
					<label class="control-label" for="deployment-url">Server URL</label>
			  		<div class="controls">
				  		<input name="url" id="add-deployment-url" />
				  	</div>
			  	</div>
				<div class="control-group">
					<label class="control-label" for="deployment-branches">Branch</label>
			  		<div class="controls">
					  	<select name="branches" id="add-deployment-branches" multiple="multiple">
					  		<?php
					  			foreach ($git->branches() as $branch) {
					  				print '<option value="'.$branch.'">'.$branch.'</option>';
					  			}
					  		?>
					  	</select>
				  	</div>
			  	</div>
	  		</fieldset>
  		</form>
  </div>
  <div class="modal-footer">
	 <button class="btn btn-primary btn-save">Add</button>
	 <button class="btn" data-dismiss="modal">Close</button>
  </div>
</div>

<div class="modal" id="deployments-success" style="max-height: 600px;">
  <div class="modal-header">
	 <a class="close" data-dismiss="modal">×</a>
	 <h3>Success</h3>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
	 <button class="btn" data-dismiss="modal">Close</button>
  </div>
</div>