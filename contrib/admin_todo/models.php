<?php
/*
 * Tikapot Todo App Models
 *
 */

require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");

class Todo_Item extends Model
{
	public function __construct() {
		parent::__construct();
		$this->add_field("content", new CharField(650));
		$this->add_field("priority", new ChoiceField(array(
			"1" => "High",
			"2" => "Medium",
			"3" => "Low",
		), "2"));
		$this->add_field("added_on", new DateTimeField(true));
		$this->add_field("assigned_to", new FKField("auth.User"));
		$this->add_field("due_on", new DateTimeField(false));
		$this->add_field("completed", new BooleanField(false));
		$this->add_field("completed_on", new DateTimeField(false));
		$this->add_field("completed_by", new FKField("auth.User"));
	}
	
	public static function objects() {
		return parent::objects()->order_by(array("priority", "ASC"));
	}
	
	public static function add($request, $content, $user = null) {
		if ($user === null)
			$user = $request->user;
		$item = new TodoItem();
		$item->content = $content;
		$item->assigned_to = $user->pk;
		return $item->save();
	}
	
	public function complete($request) {
		$this->completed = true;
		$this->completed_on = date(DateTimeField::$FORMAT);
		$this->completed_by = $request->user->pk;
		return $this->save();
	}
}

?>

