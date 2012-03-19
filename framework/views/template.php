<?php
/*
 * Tikapot Template View
 *
 */

require_once(home_dir . "framework/view.php");

class TemplateView extends View
{
	protected static $custom_tags = array(), $custom_vars = array();
	protected $title;
	
	public function __construct($url, $page, $title = "") {
		parent::__construct($url, $page);
		$this->title = $title;
	}
	
	// We have the custom tags here so people can override tags
	// for specific views if they wish
	public static function register_tag($tag) {
		TemplateView::$custom_tags[] = $tag;
	}
	
	public static function register_var($name, $value) {
		TemplateView::$custom_vars[$name] = $value;
	}
	
	public function pre_render($request, $args = array()) {
		$request->add_val("title", $this->title);
		ob_start();
	}
	
	public function post_render($request, $args = array()) {
		$tpl_output = ob_get_clean();
		return $this->parse_page($request, $args, $tpl_output, $tpl_output);
	}
	
	public function parse_page($request, $args, $tpl_output, $page) {
		// Do we extend anything? (Note: doesnt yet do nested extending)
		preg_match('/{% extends \"(?P<page>[[:punct:]\w]+)\" %}/', $page, $matches);
		if (isset($matches['page'])) {
			$tpl_parent = $matches['page'];
			ob_start();
			include(home_dir . $matches['page']);
			$page = ob_get_clean();
		}
		
		// Do we want to set an app (for local i18n etc)
		$local_app = "";
		preg_match('/{% set_app \"(?P<app>[[:punct:]\w]+)\" %}/', $tpl_output, $matches);
		if (isset($matches['app'])) {
			$local_app = $matches['app'];
			$page = preg_replace('/{% set_app "'.$matches['app'].'" %}/', "", $tpl_output);
		}
		
		// Check blocks
		// First, a quick hack for nested blocks until a proper parser is written
		preg_match_all('/{% block (?P<block>[[:punct:]\w]+) %}(?P<content>[\S\s]*?)({% endblock \\1 %})/', $tpl_output, $matches, PREG_SET_ORDER);
		foreach($matches as $val) {
			$blk_content = $val['content'];
			if (preg_match('/{% block.parent %}/', $blk_content)) {
				preg_match('/{% block '.$val['block'].' %}(?P<content>[\S\s]*?)({% endblock '.$val['block'].' %})/', $page, $old_block);
				if (isset($old_block['content']))
					$blk_content = preg_replace("/{% block.parent %}/", $old_block['content'], $blk_content);
			}
			$page = preg_replace('/{% block '.$val['block'].' %}([\S\s]*?){% endblock '.$val['block'].' %}/', $blk_content, $page);
			
			// Fix Case 973 - Remove them from tpl_output too to ensure blocks below arent affected
			$tpl_output = preg_replace('/{% block '.$val['block'].' %}([\S\s]*?){% endblock '.$val['block'].' %}/', "", $tpl_output);
		}
		// Now do normal blocks
		preg_match_all('/{% block (?P<block>[[:punct:]\w]+) %}(?P<content>[\S\s]*?){% endblock %}/', $tpl_output, $matches, PREG_SET_ORDER);
		foreach($matches as $val) {
			$blk_content = $val['content'];
			if (preg_match('/{% block.parent %}/', $blk_content)) {
				preg_match('/{% block '.$val['block'].' %}(?P<content>[\S\s]*?){% endblock %}/', $page, $old_block);
				if (isset($old_block['content']))
					$blk_content = preg_replace("/{% block.parent %}/", $old_block['content'], $blk_content);
			}
			$page = preg_replace('/{% block '.$val['block'].' %}([\S\s]*?){% endblock %}/', $blk_content, $page);
		}
		// Clean up unused blocks, but not any left over content
		$page = preg_replace('/{% block ([[:punct:]\w]+) %}/', '', $page);
		$page = preg_replace('/{% endblock %}/', '', $page);
		$page = preg_replace('/{% endblock ([[:punct:]\w]+) %}/', '', $page);
		
		// Do we include anything? (Note: doesnt yet do nested inclusion, or allow blocks)
		preg_match_all('/{% include \"(?P<page>[[:punct:]\w]+)\" %}/', $page, $matches, PREG_SET_ORDER);
		foreach($matches as $val) {
			ob_start();
			include(home_dir . $val['page']);
			$include = ob_get_clean();
			$inc_page = str_replace("/", "\\/", $val['page']);
			$page = preg_replace('/{% include "'.$inc_page.'" %}/', $include, $page);
		}
		
		// Clear out comments
		$page = preg_replace('/{% comment %}([\S\s]*?){% endcomment %}/', '', $page);
		
		// Check vars
		foreach (TemplateView::$custom_vars as $name => $val) {
			$page = str_replace("{{{$name}}}", $val, $page);
		}
		foreach ($request->safe_vals as $name => $val) {
			$page = str_replace("{{{$name}}}", $val, $page);
		}
		
		// Check i18n
		preg_match_all('/{% (?P<reach>[[:punct:]\w]*?)i18n "(?P<var>[[:punct:]\w\s]+?)" %}/', $page, $matches, PREG_SET_ORDER);
		foreach($matches as $val) {
			$replace = $request->i18n[$val['var']];
			if (isset($val['reach'])) {
				$reach = substr($val['reach'], 0, -1);
				if ($reach == "local" && $local_app !== "")
					$replace = $request->i18n[$local_app][$val['var']];
			}
			// Ensure any tags in the i18n string are taken care of
			foreach ($request->safe_vals as $tag_name => $tag_val)
				$replace = str_replace("{{{$tag_name}}}", $tag_val, $replace);
			$reach = isset($val['reach']) ? $val['reach']: "";
			$page = preg_replace('/{% '.$reach.'i18n "'.$val['var'].'" %}/', $replace, $page);
		}
		
		// Here is a good place to run any custom tags
		foreach (TemplateView::$custom_tags as $tag) {
			$page = $tag->render($request, $args, $page, $local_app);
		}
		
		// Finished!
		return $page;
	}
}
?>
