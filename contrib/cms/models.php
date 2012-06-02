<?php
/*
 * Tikapot Models
 *
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");

class ContentField extends TextField
{
	public function get_formfield($name) {
		return new TextFormField($name, $this->get_value(), array("extra" => 'style="width: 650px; height: 550px;"'));
	}
}

class CMS_Template extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("title", new CharField(650));
		$this->add_safe_field("content", new ContentField());
		$this->add_field("created", new DateTimeField(true));
		$this->add_field("created_by", new FKField("auth.User"));
		$this->add_field("updated", new DateTimeField(true, true));
		$this->add_field("updated_by", new FKField("auth.User"));
	}
	
	public function __toString() {
		return $this->title;
	}
}

class CMS_Page extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("template", new FKField("cms.CMS_Template"));
		$this->add_field("title", new CharField(650));
		$this->add_safe_field("content", new ContentField());
		$this->add_field("url", new CharField(650));
		$this->add_field("published", new BooleanField(false));
		$this->add_field("created", new DateTimeField(true));
		$this->add_field("created_by", new FKField("auth.User"));
		$this->add_field("updated", new DateTimeField(true, true));
		$this->add_field("updated_by", new FKField("auth.User"));
	}
	
	public function __toString() {
		return $this->title;
	}
}
?>

