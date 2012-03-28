<?php
/*
 * Init Script
 *
 */
 
require_once(home_dir . "framework/signal_manager.php");
require_once(home_dir . "contrib/admin/core.php");
require_once(home_dir . "contrib/admin_notes/models.php");

class NotePanel extends AdminIndexPanel
{
	protected $obj;
	
	public function __construct($title, $obj) {
		$this->obj = $obj;
		parent::__construct($title);
	}
	
	public function render($request) {
		return $this->obj->content;
	}
}

foreach (Admin_Note::objects() as $item) {
	AdminManager::register_panel(new NotePanel($GLOBALS['i18n']['admin_notes']['admin_notes_title'], $item));
}
?>

