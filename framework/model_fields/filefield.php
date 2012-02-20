<?php
/*
 * Tikapot File Field
 *
 */

require_once(home_dir . "framework/model_fields/charfield.php");
require_once(home_dir . "framework/form_fields/init.php");

class FileField extends CharField
{
	private $location, $extensions;
	
	public function __construct($location, $extensions = array(), $_extra = "") {
		if (!is_array($extensions))
			throw new Exception($GLOBALS["i18n"]["fielderr15"]);
		$this->location = $location;
		$this->extensions = $extensions;
		parent::__construct(strlen($location) + 500, "", $_extra);
	}
	
	public function __toString() {
		return $this->get_filename();
	}
	
	public function get_filename() {
		return basename($this->get_value());
	}
	
	public function get_full_filename() {
		return $this->location . basename($this->get_value());
	}
	
	public function get_form_value() {
		return $this->get_filename();
	}
	
	public function get_location() {
		return $this->location;
	}
	
	public function get_extensions() {
		return $this->extensions;
	}
	
	public function set_value($value) {
		return parent::set_value(basename($value));
	}
	
	public function get_formfield($name) {
		return new FileUploadFormField($name, $this->location, $this->extensions);
	}
	
	public function validate() {
		return parent::validate();
	}
}

class ImageField extends FileField
{
	public function __construct($location, $extensions = array("jpg", "jpeg", "png", "bmp", "gif"), $_extra = "") {
		parent::__construct($location, $extensions, $_extra);
	}
	
	public function get_formfield($name) {
		return new ImageFileUploadFormField($name, $this->get_location(), $this->get_extensions());
	}
}

?>
