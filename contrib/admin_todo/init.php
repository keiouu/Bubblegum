<?php
/*
 * Tikapot Todo App Init Script
 *
 */

require_once(home_dir . "contrib/admin/core.php");
require_once(home_dir . "contrib/admin_todo/models.php");

class TodoSidebar extends AdminSidebar
{
	public function render($request) {
		$render_data = "";
		$items = Todo_Item::find(array("completed" => false));
		if ($items->count() > 0) {
			$render_data .= '<ul>';
			$count = 0;
			foreach ($items as $item) {
				if ($count >= 4)
					break;
				$render_data .= '<li><a href="'.home_url.'admin/todo/Todo_Item/edit/'.$item->pk.'/">'.$item->content.'</a></li>';
				$count++;
			}
			if ($items->count() > $count)
				$render_data .= '<li><a href="'.home_url.'admin/todo/Todo_Item/">'.$request->i18n['admin_todo_message'].'</a></li>';
			$render_data .= '</ul>';
		}
		return $render_data;
	}
}

AdminManager::register_sidebar(new TodoSidebar($GLOBALS['i18n']['admin_todo_title']));
?>

