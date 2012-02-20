<?php
/*
 * Tikapot Note App Forms
 *
 */

require_once(home_dir . "framework/forms.php");
require_once(home_dir . "contrib/admin_notes/models.php");

class AdminNoteForm extends Form
{
	public function __construct() {
		parent::__construct(array(
			new Fieldset("Note", array(
				"content" => new TextFormField("", "", array("xlarge" => true, "extra" => 'style="width:500px; height: 200px;"'))
			)),
		));
	}
	
	public function save($model, $request) {
		$new = !$model->fromDB();
		$result = parent::save($model, $request);
		if ($new) {
			$model->created_by = $request->user;
			$model->save();
		}
		return $result;
	}
}
?>

