<?php
/*
 * Tikapot Debug Tools
 *
 */

require_once(home_dir . "framework/profiler.php");

class DebugTools
{
	public static function render($request, $args, $page, $debug_info) {
		$tpl = new TemplateView("/debug_info/", home_dir . "framework/templates/debug_panel.php");
		
		// Add any vars we may need
		$debug_text = "";
		if (isset($GLOBALS['console']))
			foreach ($GLOBALS['console'] as $val)
				$debug_text .= trim($val) . '<br/>';
		$debug_info = trim($debug_info);
		if (strlen($debug_info) > 0)
			$debug_text .= $GLOBALS['i18n']['framework']["debug_scriptout"] . '<br/>' . $debug_info;
		$tpl->register_var("debug_info", strlen($debug_text) > 0 ? $debug_text : $GLOBALS['i18n']['framework']["debug_nooutput"]);
		$tpl->register_var("debug_info_count", strlen($debug_text) > 0 ? '(' . count(preg_split("/(<br\s*\/>|<br>)/", $debug_text)) . ')' : '');
		$tpl->register_var("db_queries", ProfileData::$db_total_queries);
		$query_info = "";
		$queries = ProfileData::$db_queries;
		arsort($queries);
		foreach ($queries as $query => $count)
			$query_info .= '"' . $query .'" : ' . $count . '<br>';
		$tpl->register_var("db_info", $query_info);
		
		$tpl->setup($request, $args);
		$tpl->pre_render($request, $args);
		$tpl->render($request, $args);
		$box = $tpl->post_render($request, $args);
		
		$page = preg_replace('/\<\/body(\s*)\>/i', $box . '</body>', $page);
		print $page;
	}
}

?>
