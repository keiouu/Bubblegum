<?php
/*
 * Tikapot Models
 *
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");

class Admin_Note extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("content", new CharField(650));
		$this->add_field("created", new DateTimeField(true));
		$this->add_field("created_by", new FKField("auth.User", false));
	}
	
	public static function _display_name() {
		return parent::_display_name("Note");
	}
}
?>

