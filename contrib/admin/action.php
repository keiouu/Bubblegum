<?php
/*
 * Tikapot Admin "Actions"
 */

class AdminAction
{
	protected $_title, $_global;
	
	public function __construct($title, $global = false) {
		$this->_title = $title;
		$this->_global = $global;
	}
	
	public function is_global() {
		return $this->_global;
	}
	
	public function get_title() {
		return $this->_title;
	}
	
	public function render($request, $model) { return '<button class="btn primary">'.$this->title.'</button>'; }
}

class GlobalAdminAction extends AdminAction
{
	public function __construct($title) {
		parent::__construct($title, true);
	}
}
?>
