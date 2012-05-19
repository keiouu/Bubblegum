<?php
/*
 * Tikapot Template Date Tag
 *
 */

require_once(home_dir . "framework/template_tags/tag.php");

class DateTag extends TplTag
{
	public function render($request, $args, $page) {
		preg_match_all('/{% date "(?P<var>[\s[:punct:]\w]+?)" %}/', $page, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$date = date($val['var']);
			$page = preg_replace('/{% date "'.$val['var'].'" %}/', $date, $page);
		}
		return $page;
	}
}

?>

