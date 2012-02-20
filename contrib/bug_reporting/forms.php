<?php
/*
 * Forms
 *
 */

require_once(home_dir . "framework/forms.php");
require_once(home_dir . "framework/form_fields/init.php");
require_once(home_dir . "contrib/bug_reporting/models.php");

class BugAddForm extends Form
{
	public function __construct() {
		parent::__construct(array(
			new Fieldset("", array(
					"content" => new TextFormField("", "", array("helptext" => "Whats wrong?")),
			)),
		), "?bugreport=true");
	}
}

?>
