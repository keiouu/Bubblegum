<?php
/*
 * Tikapot Todo App Forms
 *
 */

require_once(home_dir . "framework/forms.php");
require_once(home_dir . "contrib/auth/models.php");
require_once(home_dir . "contrib/admin_todo/models.php");

class TodoItemForm extends Form
{
	public function get_fieldset() {
		$dummy = new Todo_Item();
		$select_field = SelectFormField::from_model("Assigned To", new User(), array("helptext" => "Who should this task be assigned to?"));
		return (new Fieldset("Todo", array(
				"content" => new CharFormField("", "", array("helptext" => "What do you need to do?", "xlarge" => true)),
				"priority" => new SelectFormField("Priority", $dummy->_priority->get_choices(), "", array("helptext" => "How important is this?")),
				"assigned_to" => $select_field,
				"due_on" => new DateTimeFormField("Due On", "".date(DateTimeField::$FORMAT)),
		)));
	}
	
	public function __construct() {
		parent::__construct(array($this->get_fieldset()));
	}
}

class TodoItemEditForm extends TodoItemForm
{
	public function get_fieldset() {
		$fieldset = parent::get_fieldset();
		$fieldset->add_item("completed", new CheckedFormField("Completed?", ""));
		return $fieldset;
	}
	
	public function save($model, $request) {
		$completed = $model->completed;
		$result = parent::save($model, $request);
		if (!$completed && $model->completed)
			$model->complete($request);
		return $result;
	}
}
?>

