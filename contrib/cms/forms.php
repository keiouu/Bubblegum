<?php
/*
 * Forms
 *
 */

require_once(home_dir . "framework/forms.php");
require_once(home_dir . "framework/form_fields/init.php");
require_once(home_dir . "contrib/cms/models.php");

class CMSTemplateAddForm extends Form
{
	protected function get_fieldset() {
		return (new Fieldset("Todo", array(
				"title" => new CharFormField("Title", "", array("xlarge" => true)),
				"content" => new TextFormField("Content", "<html>\n\t<head>\n\t\t<title>{{title}}</title>\n\t</head>\n\t<body>\n\t\t{% block content %}{% endblock content %}\n\t</body>\n</html>", array("extra" => 'style="width: 650px; height: 550px;"')),
		)));
	}
	
	public function __construct() {
		parent::__construct(array($this->get_fieldset()));
	}
}

?>
