<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");

class CMSView extends TemplateView
{
	private $cms_page;
	
	public function __construct($page) {
		$this->cms_page = $page;
		parent::__construct($this->cms_page->url, "", $this->cms_page->title);
	}
	
	public function pre_render($request, $args) {
		parent::pre_render($request, $args);
	}
	
	public function render($request, $args) {
		print $this->cms_page->content;
	}
	
	public function post_render($request, $args = array()) {
		$content = ob_get_clean();
		return $this->parse_page($request, $args, $content, $this->cms_page->template->content);
	}
}
?>

