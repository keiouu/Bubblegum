<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/models.php");
require_once(dirname(__FILE__) . "/forms.php");

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
	}
}

function sort_activity($arr) {

}

class AJAX_ActivityFeedView extends View
{
	public function setup($request, $args) {
		return $request->user->logged_in();
	}
	
	public function render($request, $args) {
		$team_joins = Team_Link::objects()->order_by(array("joined", "DESC"))->limit(10);
		$closed_tasks = Task::objects()->find(array("progress" => 100))->order_by(array("completed", "DESC"))->limit(10);
		$created_tasks = Task::objects()->order_by(array("created", "DESC"))->limit(10);
		// TODO - Milestone progress - 25, 50, 75, 100
		
		$activity = array();
		foreach ($team_joins as $obj) {
			$activity[] = array(
				$obj->joined,
				$obj->user->get_short_display_name() . " joined ".$obj->team."!"
			);
		}
		foreach ($closed_tasks as $obj) {
			$activity[] = array(
				$obj->completed,
				$obj->completed_by->get_short_display_name() . " completed ".$obj->name."!"
			);
		}
		foreach ($created_tasks as $obj) {
			$activity[] = array(
				$obj->created,
				$obj->created_by->get_short_display_name() . " added task ".$obj->name."!"
			);
		}
		
		usort($activity, "sort_activity");
		
		$i = 0;
		foreach ($activity as $data) {
			if ($i >= 10)
				break;
			print '<li>&raquo; <a href="'.home_url.'">'.$data[1].'</a></li>';
			$i++;
		}
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
			if ($tasks->count() > 0) {
				$max_progress = $tasks->count() * 100;
				$progress = ($total_progress / $max_progress) * 100;
			} else {
				$progress = 0;
			}
			$request->dataset[] = array(
				"name" => $milestone->name,
				"progress" => '<div class="progress progress-'.($progress <= 25 ? 'danger' : ($progress >= 75 ? 'success' : 'info')).' progress-striped active">
						<div class="progress-text">'.round($progress, 0).'%</div>
						<div class="bar" style="width:'.$progress.'%;"></div>
					</div>'
			);
		}
		return $request->user->logged_in();
	}
}

class AJAX_MileStoneAddView extends View
{
	public function setup($request, $args) {
		$request->project = Project::get_or_ignore($args['project']);
		return $request->project && $request->user->logged_in();
	}
	
	public function render($request, $args) {
		$form = new MileStoneAddForm();
		if ($form->load_post_data($request->post)) {
			list($o, $c) = MileStone::get_or_create(array(
				"project" => $request->project->pk,
				"name" => $form->get_value("name"),
				"description" => $form->get_value("description"),
			));
			if ($c)
				print 'Success!';
			else
				print 'A milestone with that name already exists!';
		} else {
			print 'Error loading data!';
		}
	}
}

class AJAX_TasksView extends JSONView
{
	public function setup($request, $args) {
		$request->dataset = array();
		$request->project = Project::get_or_ignore($args['project']);
		if (!$request->project)
			$tasks = Task::objects()->order_by(array("priority"));
		else
			$tasks = Task::objects()->filter(array("project" => $request->project->pk))->order_by(array("priority"));
		if (isset($request->get['milestone'])) {
			$milestone = Milestone::get_or_ignore(array("name" => $request->get['milestone']));
			if ($milestone)
				$tasks = $tasks->filter(array("milestone" => $milestone->pk));
		}
		if ($tasks->count() == 0)
			die('{"error":"No Data!"}');
		foreach ($tasks as $task) {
			if ($task->progress >= 100 || (isset($request->get['own_tasks_only']) && !$task->assigned($request->user)))
				continue;
			
			$request->dataset[] = array(
				"milestone" => $task->milestone->__toString(),
				"name" => $task->name,
				"type" => $task->_type->__toString(),
				"priority" => $task->_priority->__toString(),
				"status" => $task->_status->__toString(),
				"name" => $task->name,
				"progress" => '<div class="progress progress-'.($task->progress <= 25 ? 'danger' : ($task->progress >= 75 ? 'success' : 'info')).' progress-striped active">
						<div class="progress-text">'.$task->progress.'%</div>
						<div class="bar" style="width:'.$task->progress.'%;"></div>
					</div>',
				"assignees" => $task->assignees(),
			);
		}
		return $request->user->logged_in();
	}
}

class AJAX_TaskEditView extends View
{
	public function setup($request, $args) {
		return $request->user->logged_in() && isset($request->post['csrf']) && $request->validate_csrf_token($request->post['csrf']);
	}
	
	public function render($request, $args) {
		print $request->get_csrf_token();
		$project = Project::get_or_ignore($args['project']);
		$task = Task::get_or_ignore($request->post['pk']);
		if ($project && $task) {
			foreach ($request->post as $var => $val) {
				if (isset($task->$var)) {
					if ($var == "progress" && $val === 100 && $task->progress < 100) {
						$task->completed = true;
						$task->completed_by = $request->user;
					}
					$task->$var = $val;
				}
			}
			$task->save();
			$assignees = explode(",", $request->post['assignees']);
			foreach ($assignees as $assignee) {
				list($type, $pk) = explode("|", $assignee);
				if (class_exists($type)) {
					$obj = null;
					if ($type == "Team")
						$obj = Team::get_or_ignore(array("pk" => $pk));
					if ($type == "User")
						$obj = User::get_or_ignore(array("pk" => $pk));
					if ($obj) {
						Task_Link::get_or_create(array(
							"task" => $task->pk,
							"assignee" => $type . "|" . $obj->pk,
						));
					}
				}
			}
		}
	}
}

class AJAX_TaskDetailView extends JSONView
{
	public function setup($request, $args) {
		$request->dataset = array();
		$request->project = Project::get_or_ignore($args['project']);
		if (!$request->project)
			die('{"error":"Incorrect Project!"}');
			
		if (isset($request->get['pk']))
			$task = Task::get_or_ignore(array("pk" => $request->get['pk']));
			
		if (isset($request->get['name']))
			$task = Task::get_or_ignore(array("name" => $request->get['name']));
			
		if (isset($task)) {
			$request->dataset[] = array(
				"pk" => $task->pk,
				"milestone" => $task->milestone->__toString(),
				"name" => $task->name,
				"description" => $task->description,
				"type" => $task->type,
				"priority" => $task->priority,
				"status" => $task->status,
				"type_string" => $task->_type->__toString(),
				"priority_string" => $task->_priority->__toString(),
				"status_string" => $task->_status->__toString(),
				"name" => $task->name,
				"progress" => $task->progress,
				"assignees" => $task->assignees(),
				"assignees_full" => $task->assignees(true),
			);
		}
		return $request->user->logged_in();
	}
}

class AJAX_ProjectDetailView extends JSONView
{
	public function setup($request, $args) {
		$request->dataset = array();
		$request->project = Project::get_or_ignore($args['project']);
		if (!$request->project)
			die('{"error":"Incorrect Project!"}');
		
		$milestones = array();
		foreach (Milestone::find(array("project" => $request->project->pk)) as $milestone) {
			$milestones[$milestone->pk] = $milestone->__toString();
		}
		
		$request->dataset[] = array(
			"name" => $request->project->name,
			"milestones" => $milestones,
		);
		
		return $request->user->logged_in();
	}
}
?>

