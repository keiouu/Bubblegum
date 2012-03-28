<?php
/*
 * Tikapot DateTime Field
 *
 */

require_once(home_dir . "framework/model_fields/modelfield.php");
require_once(home_dir . "framework/model_fields/datefield.php");

class DateTimeField extends DateField
{
	public static $TIME_FORMAT = "H:i:s";
	public static $FORMAT = "Y-m-d H:i:s";
	protected static $db_type = "timestamp";
	
	public function get_formfield($name) {
		return new DateTimeFormField($name, $this->get_value());
	}
	
	public function get_time() {
		return date(DateTimeField::$TIME_FORMAT, strtotime($this->get_value()));
	}
	
	public function validate($val = NULL) {
		$val = ($val === NULL) ? $this->get_value() : $val;
		if (strlen($val) == 0)
			return True;
		$regex = "/^(\d{4})(-)(\d{2})(-)(\d{2})\x20(\d{2})(:)(\d{2})(:)(\d{2})$/";
		$valid = preg_match($regex, $val) == 1;
		if (!$valid)
			array_push($this->errors, $GLOBALS['i18n']['framework']["fielderr4"]);
		return $valid;
	}
	
	public function get_readable_value() {
		$date1 = new DateTime($this->get_time());
		$date2 = new DateTime("now");
		$interval = $date1->diff($date2);
		$time = $interval->s . " seconds ago";
		if ($interval->y > 0)
			$time = $interval->y . " years ago";
		elseif ($interval->m > 0)
			$time = $interval->m . " months ago";
		elseif ($interval->d > 0)
			$time = $interval->d . " days ago";
		elseif ($interval->h > 0)
			$time = $interval->h . " hours ago";
		elseif ($interval->i > 0)
			$time = $interval->i . " minutes ago";			
		return $time;
	}
}

?>

