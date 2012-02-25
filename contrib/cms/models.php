<?php
/*
 * Tikapot Models
 *
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");

class ContentField extends TextField
{
	private static $replacables = array(
		">" => "&gt;",
		"<" => "&lt;",
		"{" => "&#123;",
		"}" => "&#125;",
		"%" => "&#37;",
		"'" => "&#39;",
		"\"" => "&#34;",
	);
	
	public function get_formfield($name) {
		return new TextFormField($name, $this->get_value(), array("extra" => 'style="width: 650px; height: 550px;"'));
	}
	
	public static function sanitize($string) {
		foreach (ContentField::$replacables as $var => $val)
			$string = str_replace($var, $val, $string);
		return $string;
	}
	
	public static function desanitize($string) {
		foreach (ContentField::$replacables as $var => $val)
			$string = str_replace($val, $var, $string);
		return $string;
	}
	
	public function get_value() {
		return ContentField::desanitize($this->value);
	}
	
	public function set_value($value) {
		// Get over firewalls
		$value = str_replace("\\\"", "\"", $value);
		$value = str_replace("\\'", "'", $value);
		return parent::set_value($value);
	}
	
	public function get_real_value() {
		return $this->get_value();
	}
	
	public function sql_value($db, $val = NULL) {
		$val = ($val === NULL) ? $this->value : $val;
		return parent::sql_value($db, ContentField::sanitize($val));
	}
	
	public function get_form_value() {
		return ContentField::sanitize($this->value);
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

