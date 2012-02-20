<?php
/*
 * Tikapot Models
 *
 */
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");

class Admin_log extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("user", new FKField("auth.user"));
		$this->add_field("action", new ChoiceField(array(
			"0" => "create",
			"1" => "edit",
			"2" => "delete",
			"3" => "login",
			"4" => "register",
			"5" => "update",
			"6" => "upgrade",
		), "0"));
		$this->add_safe_field("detail", new TextField());
		$this->add_field("timestamp", new DateTimeField($auto_now_add = True));
	}
	
	public static function objects() {
		return parent::objects()->order_by(array("timestamp", "DESC"));
	}
}
?>

