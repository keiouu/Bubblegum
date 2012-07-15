<?php
/*
 * Forms
 *
 */

require_once(home_dir . "framework/forms.php");
require_once(home_dir . "framework/form_fields/init.php");
require_once(dirname(__FILE__) . "/models.php");

class MileStoneAddForm extends Form2
{
	public function __construct() {
		parent::__construct("");
		$this->fieldset("")
			->append("name", "Name: ", "char")
			->append("description", "Description: ", "textarea");
	}
}

/*class MileStoneAddForm extends Form
{
	protected function get_fieldset() {
		$dummy = new Task();
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
}*/

?>
