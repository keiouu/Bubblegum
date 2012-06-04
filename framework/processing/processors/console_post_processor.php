<?php

require_once(home_dir . "framework/processing/post_processor.php");
require_once(home_dir . "framework/profiler.php");
require_once(home_dir . "framework/views/template.php");

/**
 * Replace </body> with Console Code
 */
class Console_Post_Processor extends Post_Processor
{	
	/**
	 * Take $data and modify it to include the TP console
	 * 
	 * @param Framework\Request $data The request object to work on
	 */
	public function modify($data) {
		$console = $this->get_console($data);
		$data->output = preg_replace('/\<(\s*)\/body(\s*)\>/i', $console . '</body>', $data->output);
	}
	
	private function get_console($request) {
		$tpl = new TemplateView("/tikapot/debug_info/", home_dir . "framework/templates/debug_panel.php");
		
		// Add any vars we may need
		$debug_text = "";
		if (isset($GLOBALS['console']))
			foreach ($GLOBALS['console'] as $val)
				$debug_text .= trim($val) . '<br/>';
		
		$debug_info = trim(ob_get_clean()); // TODO - dont like this. maybe dont support this? use console_ methods etc.
		if (strlen($debug_info) > 0)
			$debug_text .= $GLOBALS['i18n']['framework']["debug_scriptout"] . '<br/>' . $debug_info;
		
		$tpl->register_var("debug_info", strlen($debug_text) > 0 ? $debug_text : $GLOBALS['i18n']['framework']["debug_nooutput"]);
		$tpl->register_var("debug_info_count", strlen($debug_text) > 0 ? '(' . (count(preg_split("/(<br\s*\/>|<br>)/", $debug_text))-1) . ')' : '');
		$tpl->register_var("db_queries", ProfileData::$db_total_queries);
		
		$query_info = "";
		$queries = ProfileData::$db_queries;
		arsort($queries);
		foreach ($queries as $query => $count)
			$query_info .= '"' . $query .'" : ' . $count . '<br>';
		$tpl->register_var("db_info", $query_info);
		
		$tpl->setup($request, $request->args);
		$tpl->pre_render($request, $request->args);
		$tpl->render($request, $request->args);
		return $tpl->post_render($request, $request->args);
	}
}
