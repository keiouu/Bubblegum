<?php
/*
 * Tikapot Char Form Field
 *
 */

require_once(home_dir . "framework/form_fields/formfield.php");

class CharFormField extends FormField
{
	public function get_type() {
		return "text";
	}
	
	protected function get_field_class() {
		return "charfield";
	}
}

class CaptchaFormField extends CharFormField
{
	private $image_location, $image_url, $width, $height;
	
	public function __construct($name, $initval = "", $options = array()) {
		parent::__construct($name, $initval, $options);
		$this->width = isset($options['width']) ? $options['width'] : 200;
		$this->height = isset($options['height']) ? $options['height'] : 70;
		$this->options['placeholder'] = $GLOBALS["i18n"]["captchaplaceholder"];
	}
	
	private function get_string($length = 7) { 
		$rand_src = array(array(48,57), array(97,122)); 
		srand((double) microtime() * 245167413); 
		$random_string = ""; 
		for($i = 0; $i < $length; $i++){ 
			$i1 = rand(0, sizeof($rand_src) - 1); 
			$random_string .= chr(rand($rand_src[$i1][0], $rand_src[$i1][1])); 
		} 
		return $random_string; 
	}
	
	public function validate($base_id, $safe_name) {
		$id = $this->get_field_id($base_id, $safe_name);
		if(isset($_SESSION["captcha"][$id]) && $this->get_value() == $_SESSION["captcha"][$id])
			return true;
		$this->set_error($GLOBALS["i18n"]["captchaerr"]);
		return false;
	}
	
	public function get_image($base_id, $safe_name) {
		$id = $this->get_field_id($base_id, $safe_name);
		if(!isset($_SESSION["captcha"]) || !is_array($_SESSION["captcha"]))
			$_SESSION["captcha"] = array();
		if (!isset($_SESSION["captcha"][$id]))
			$_SESSION["captcha"][$id] = $this->get_string(7);
		return '<br /><img src="'.home_url.'tikapot/api/captcha/?sesid='.$id.'&width='.$this->width.'&height='.$this->height.'" alt="CAPTCHA image" />';
	}
	
	public function get_raw_input($base_id, $safe_name) {
		return parent::get_input($base_id, $safe_name);
	}
	
	public function get_input($base_id, $safe_name) {
		// Return an image
		return $this->get_image($base_id, $safe_name) . '<br />' . $this->get_raw_input($base_id, $safe_name) ;
	}
	
	protected function get_field_class() {
		return "captchafield";
	}
}

class HiddenFormField extends FormField
{
	public function get_type() {
		return "hidden";
	}
	
	protected function get_field_class() {
		return "hiddenfield";
	}
}

class PasswordFormField extends FormField
{
	public function get_type() {
		return "password";
	}
	
	protected function get_field_class() {
		return "passwordfield";
	}
}

class PasswordWithStrengthFormField extends PasswordFormField
{
	protected function get_field_class() {
		return "passwordfield password-strength-field";
	}
	
	public function get_input($base_id, $safe_name) {
		$id = $base_id . '_' . $safe_name;
		return '<span class="password-strength-field-container">' . parent::get_input($base_id, $safe_name) . '<span style="display: none;" id="'.$id.'_message"></span></span>
		<script type="text/javascript">
			var strength_descs = new Array();
			strength_descs[0] = "'.$GLOBALS['i18n']['password_strength_0'].'";
			strength_descs[1] = "'.$GLOBALS['i18n']['password_strength_1'].'";
			strength_descs[2] = "'.$GLOBALS['i18n']['password_strength_2'].'";
			strength_descs[3] = "'.$GLOBALS['i18n']['password_strength_3'].'";
			strength_descs[4] = "'.$GLOBALS['i18n']['password_strength_4'].'";
			strength_descs[5] = "'.$GLOBALS['i18n']['password_strength_5'].'";
		
			document.getElementById("'.$id.'").onkeydown = function() {
				var span = document.getElementById("'.$id.'_message");
				span.style.display = "inline-block";
				
				var strength = 0;
				if (this.value.length > 6) strength++;
				if (this.value.length > 10) strength++;
				if (this.value.match(/.[^,!,$,#,%,@,&,(,),_,-,~,*,?]/)) strength++;
				if (this.value.match(/[a-z]/) && this.value.match(/[A-Z]/)) strength++;
				if (this.value.match(/\d+/)) strength++;
				
				span.innerHTML = strength_descs[strength];
			};
		</script>';
	}
}

class FileUploadFormField extends FormField
{
	protected $location, $types;
	
	public function __construct($name, $location, $types, $initial_value = "", $options = array()) {
		$this->location = $location;
		$this->types = $types;
		parent::__construct($name, $initial_value, $options);
	}
	
	public function validate($base_id, $safe_name) {
		return !$this->has_error();
	}
	
	public function set_value($val) {
		if (is_array($val) && isset($val['tmp_name'])) {
			// Check type
			$type = substr(strrchr($val['name'], '.'), 1);
			if (!in_array($type, $this->types)) {
				$this->set_error($GLOBALS['i18n']["fielderr18"]);
				return;
			}
			
			// Choose the name
			$filename = $this->location . basename($val['name'], "." . $type);
			
			// Ensure the file doesnt exist
			$old_filename = $filename;
			$i = 0;
			while (file_exists($filename . "." . $type)) {
				$filename = $old_filename . "_" . $i;
				$i++;
			}
			
			// Upload the file
			$filename .= "." . $type;
			if (@move_uploaded_file($val['tmp_name'], $filename)) {
				return parent::set_value($filename);
			} else {
				$this->set_error($GLOBALS['i18n']["fielderr17"] . " " . (isset($php_errormsg) ? $php_errormsg : $GLOBALS['i18n']["error2"]));
				return;
			}
		}
		return parent::set_value($val);
	}
	
	public function get_value() {
		return parent::get_value();
	}
	
	public function get_type() {
		return "file";
	}
	
	public function claim_own($my_name, $field_name, $field_value) {
		if ($field_name == $my_name . "_check") {
			$this->set_value("");
			return true;
		}
		return parent::claim_own($my_name, $field_name, $field_value);
	}
	
	public function get_input($base_id, $safe_name, $classes = "") {
		$field = parent::get_input($base_id, $safe_name, $classes);
		$check_field_id = $this->get_field_id($base_id, $safe_name) . "_check";
		$field .= '<span class="checkfield_remove">
		<input type="checkbox" id="'.$check_field_id.'" name="'.$check_field_id.'" value="0" class="checkedfield" /> '.$GLOBALS['i18n']['remove'].'</span>';
		return $field;
	}
	
	protected function get_field_class() {
		return "filefield";
	}
}

class ImageFileUploadFormField extends FileUploadFormField
{
	public function __construct($name, $location, $types = array("jpg", "jpeg", "png", "bmp", "gif"), $initial_value = "", $options = array()) {
		parent::__construct($name, $location, $types, $initial_value, $options);
	}
}

class CheckedFormField extends FormField
{
	public function __construct($name, $initial_value = false, $options = array()) {
		if ($initial_value === "")
			$initial_value = false;
		parent::__construct($name, $initial_value, $options);
	}
	
	public function get_input($base_id, $safe_name, $classes = "") {
		$field_id = $this->get_field_id($base_id, $safe_name);
		$field = '<input type="checkbox" id="'.$field_id.'" name="'.$field_id.'" value="1" class="'.$this->get_classes($safe_name, $classes).'"';
		if ($this->get_value() == true)
			$field .= ' checked="yes"';
		if ($this->get_extras() !== "")
			$field .= ' ' . $this->get_extras();
		$field .= ' />';
		return $field;
	}
	
	protected function get_field_class() {
		return "checkedfield";
	}
	
	public function pre_postdata_load() {
		$this->set_value(false);
	}
}

class SelectFormField extends FormField
{
	private $field_options;
	
	public function __construct($name, $field_options, $initial_value = "0", $options = array()) {
		if (is_array($field_options)) {
			$this->field_options = $field_options;
		} else {
			throw new Exception($GLOBALS["i18n"]["fielderr11"]);
		}
		parent::__construct($name, $initial_value, $options);
	}
	
	public static function from_model($name, $model, $options = array()) {
			$arr = array();
			$objects = $model::objects()->all();
			foreach ($objects as $object) {
				$arr[$object->pk] = $object->__toString();
			}
			return new static($name, $arr, ($model->fromDB() ? "".$model->pk : "0"), $options);
	}
	
	public function get_input($base_id, $safe_name, $classes = "") {
		$field_id = $this->get_field_id($base_id, $safe_name);
		$field = '<select id="'.$field_id.'" name="'.$field_id.'" class="'.$this->get_classes($safe_name, $classes).'"';
		if ($this->get_extras() !== "")
			$field .= ' ' . $this->get_extras();
		$field .= '>';
		foreach($this->field_options as $value => $name) {
			$field .= '<option value="'.$value.'"';
			if ($value == $this->get_value())
				$field .= ' selected="selected"';
			$field .= '>'.$name.'</option>';
		}
		$field .= "</select>";
		return $field;
	}
	
	protected function get_field_class() {
		return "selectfield";
	}
}

class FKFormField extends SelectFormField
{
	private static $_FKCache = array();
	protected $field, $model_string, $obj;
	
	public function __construct($name, $model_string, $obj, $initial_value = "", $options = array()) {
		$this->model_string = $model_string;
		$this->obj = $obj;
		$field_options = array();
		if (!isset(FKFormField::$_FKCache[get_class($obj)])) {
			$objects = $obj::objects()->all();
			foreach ($objects as $object) {
				$field_options[$object->pk] = $object->__toString();
			}
			FKFormField::$_FKCache[get_class($obj)] = $field_options;
		} else {
			$field_options = FKFormField::$_FKCache[get_class($obj)];
		}
		parent::__construct($name, $field_options, (($initial_value === "" && $this->obj->fromDB()) ? "" . $this->obj->pk : $initial_value), $options);
	}
	
	public function get_object() {
		return $this->obj;
	}
	
	public function get_model_string() {
		return $this->model_string;
	}
	
	public function get_field() {
		return $this->field;
	}
	
	protected function get_field_class() {
		return parent::get_field_class() . " fkfield";
	}
}

class TextFormField extends FormField
{
	public function get_input($base_id, $safe_name, $classes = "") {
		$field_id = $this->get_field_id($base_id, $safe_name);
		$field = '<textarea id="'.$field_id.'" name="'.$field_id.'" class="'.$this->get_classes($safe_name, $classes).'"';
		if ($this->get_placeholder() !== "")
			$field .= ' placeholder="'.$this->get_placeholder().'"';
		if ($this->get_extras() !== "")
			$field .= ' ' . $this->get_extras();
		$field .= '>';
		$field .= $this->get_display_value();
		$field .= '</textarea>';
		return $field;
	}
	
	protected function get_field_class() {
		return "textfield";
	}
}
class TextAreaFormField extends TextFormField {} // Alias

class NumberFormField extends FormField
{
	public function get_type() {
		return "number";
	}
	
	protected function get_field_class() {
		return "numberfield";
	}
}

class URLFormField extends FormField
{
	public function get_type() {
		return "url";
	}
	
	protected function get_field_class() {
		return "urlfield";
	}
}

class TelephoneFormField extends FormField
{
	public function get_type() {
		return "tel";
	}
	
	protected function get_field_class() {
		return "telephonefield";
	}
}

class EmailFormField extends FormField
{
	public function get_type() {
		return "email";
	}
	
	protected function get_field_class() {
		return "emailfield";
	}
}

class DateFormField extends FormField
{
	public function get_type() {
		return "date";
	}
	
	protected function get_field_class() {
		return "datefield";
	}
}

class DateTimeFormField extends FormField
{
	public function get_type() {
		return "datetime";
	}
	
	protected function get_field_class() {
		return "datetimefield";
	}
}

class SearchFormField extends FormField
{
	public function get_type() {
		return "search";
	}
	
	protected function get_field_class() {
		return "searchfield";
	}
}
?>

