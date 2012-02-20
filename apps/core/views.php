<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/models.php");

class BaseView extends TemplateView
{
	public function setup($request, $args) {
		if (!$request->user->logged_in()) {
			header("Location: " . home_url . "login/");
			return false;
		}
		return true;
	}
}

class LoginView extends TemplateView
{
	public function setup($request, $args) {
		if ($request->user->logged_in()) {
			header("Location: " . home_url);
			return false;
		}
		return true;
	}
}

class ProjectView extends BaseView
{
	public function setup($request, $args) {
		if (!parent::setup($request, $args))
			return false;
		$request->project = Project::get_or_ignore($args['project']);
		return true;
	}
}

class JSONView extends View
{
	public function render($request, $args) {
		if (!isset($request->dataset))
			die('{"error":"No Dataset!"');
		return json_encode($request->dataset);
		$output = '{"objects": [';
		$i = 0;
		foreach ($request->dataset as $row) {
			if ($i !== 0)
				$output .= ", ";
			$row_output = "{";
			foreach ($row as $name => $value) {
				if (strlen($row_output) > 1)
					$row_output .= ", ";
				$value = str_replace('"', '\\"', $value);
				$value = str_replace("\r", "\\r", $value);
				$value = str_replace("\n", "\\n", $value);
				$value = str_replace("\t", "\\t", $value);
				$row_output .= '"'.$name.'":"'.$value.'"';
			}
			$output .= $row_output . "}";
			$i++;
		}
		return $output . "]}";
	}
}

class AJAX_ActivityFeedView extends View
{
	public function setup($request, $args) {
		return $request->user->logged_in();
	}
	
	public function render($request, $args) {
		// Joined team
		// Closed task
		// Added task
		// Milestone progress - 25, 50, 75, 100
		print '<li><a href="{{home_url}}">Something happened!</a></li>';
	}
}

class AJAX_MileStonesView extends JSONView
{
	public function setup($request, $args) {
		$request->dataset = array();
		$request->project = Project::get_or_ignore($args['project']);
		if (!$request->project)
			die('{"error":"Incorrect Project!"}');
		$milestones = Milestone::objects()->filter(array("project" => $request->project->pk));
		if ($milestones->count() == 0)
			die('{"error":"No Data!"}');
		foreach ($milestones as $milestone) {
			$total_progress = 0;
			$tasks = Task::objects()->filter(array("milestone" => $milestone->pk));
			foreach ($tasks as $task) {
				$total_progress += $task->progress;
			}
			$max_progress = $tasks->count() * 100;
			$progress = ($total_progress / $max_progress) * 100;
			$request->dataset[] = array(
				"name" => $milestone->name,
				"progress" => '<div class="progress progress-'.($progress <= 25 ? 'danger' : ($progress >= 75 ? 'success' : 'info')).' progress-striped active">
						<div class="progress-text">'.$progress.'%</div>
						<div class="bar" style="width:'.$progress.'%;"></div>
					</div>'
			);
		}
		return $request->user->logged_in();
	}
}

class AJAX_MileStonesView_old extends View
{
	public function render($request, $args) {
		$request->project = Project::get_or_ignore($args['project']);
		if (!$request->project)
			die('{"error":"Incorrect project"}');
			//die('<tr><td colspan="2">Error - Incorrect project!</td></tr>');
		$milestones = Milestone::objects()->filter(array("project" => $request->project->pk));
		foreach ($milestones as $milestone) {
			$total_progress = 0;
			$tasks = Task::objects()->filter(array("milestone" => $milestone->pk));
			foreach ($tasks as $task) {
				$total_progress += $task->progress;
			}
			$max_progress = $tasks->count() * 100;
			$progress = ($total_progress / $max_progress) * 100;
			print '<tr>
				<td>'.$milestone->name.'</td>
				<td>
					<div class="progress progress-'.($progress <= 25 ? 'danger' : ($progress >= 75 ? 'success' : 'info')).' progress-striped active">
						<div class="progress-text">'.$progress.'%</div>
						<div class="bar" style="width:'.$progress.'%;"></div>
					</div>
				</td>
			</tr>';
		}
		if ($milestones->count() == 0)
			print '<tr><td colspan="2">No Data!</td></tr>';
	}
}

class AJAX_TasksView extends View
{
	public function render($request, $args) {
		$request->project = Project::get_or_ignore($args['project']);
		if (!$request->project)
			die('<tr><td colspan="5">Error - Incorrect project!</td></tr>');
		$tasks = Task::objects()->filter(array("project" => $request->project->pk));
		foreach ($tasks as $task) {
			if ($task->progress >= 100 || !$task->assigned($request->user))
				continue;
			
			print '<tr>
				<td>'.$task->milestone.'</td>
				<td>'.$task->name.'</td>
				<td>'.$task->_type.'</td>
				<td>'.$task->_priority.'</td>
				<td>
					<div class="progress progress-'.($task->progress <= 25 ? 'danger' : ($task->progress >= 75 ? 'success' : 'info')).' progress-striped active">
						<div class="progress-text">'.$task->progress.'%</div>
						<div class="bar" style="width:'.$task->progress.'%;"></div>
					</div>
				</td>
			</tr>';
		}
		if ($tasks->count() == 0)
			print '<tr><td colspan="5">No Data!</td></tr>';
	}
}
?>

