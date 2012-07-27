<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "contrib/bootstrap/FormPrinter.php");
require_once(dirname(__FILE__) . "/models.php");
require_once(dirname(__FILE__) . "/forms.php");
require_once(dirname(__FILE__) . "/support_handler.php");

class BubblegumView extends TemplateView
{
	public function setup($request, $args) {
		$request->media->enable_processor();
		
		$request->media->add_file(home_dir . "apps/core/media/css/prettify.css");
		
		if (isset($request->get['theme']) && isset($request->get['csrf']) && $request->validate_csrf_token($request->get['csrf'])) {
			list($theme, $created) = User_Preference::get_or_create(array("user" => $request->user, "key" => "theme"));
			$theme->value = preg_replace("/[^a-z]+/i", "", $request->get['theme']);
			$theme->save();
		}
		
		
		$useTheme = false;
		if ($request->user->logged_in()) {
			$theme = User_Preference::get_or_ignore(array("user" => $request->user, "key" => "theme"));
			if ($theme) {
				$theme = preg_replace("/[^a-z]+/i", "", $theme->value);
				if (file_exists(home_dir . "apps/core/media/css/themes/".$theme."/bootstrap.css")) {
					$useTheme = true;
					$request->media->add_file(home_dir . "apps/core/media/css/themes/".$theme."/bootstrap.css");
					$request->media->add_file(home_dir . "apps/core/media/css/themes/".$theme."/theme.css");
				}
			}
		}
		
		if (!$useTheme) {
			$request->media->add_file(home_dir . "apps/core/media/css/themes/standard/bootstrap.css");
		}
		
		
		$request->media->add_file(home_dir . "apps/core/media/css/bootstrap-responsive.css");
		$request->media->add_file(home_dir . "apps/core/media/css/style.css");
		
		$request->media->add_file(home_dir . "apps/core/media/js/jquery.min.js");
		$request->media->add_file(home_dir . "apps/core/media/js/bootstrap.min.js");
		$request->media->add_file(home_dir . "apps/core/media/js/utils.js");
		$request->media->add_file(home_dir . "apps/core/media/js/base.js");
		$request->media->add_file(home_dir . "apps/core/media/js/activity.feed.js");
		$request->media->add_file(home_dir . "apps/core/media/js/project.feeds.js");
		$request->media->add_file(home_dir . "apps/core/media/js/feeds.ajax.js");
		$request->media->add_file(home_dir . "apps/core/media/js/prettify/prettify.js");
		$request->media->add_file(home_dir . "apps/core/media/js/git-file-list.js");
		
		return parent::setup($request, $args);
	}
}

class BaseView extends BubblegumView
{
	public function setup($request, $args) {
		$setup = parent::setup($request, $args);
		if (!$request->user->logged_in()) {
			header("Location: " . home_url . "login/");
			return false;
		}
		$request->gravatar = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($request->user->email)));
		return $setup;
	}
}

class SupportView extends BubblegumView
{
	public function setup($request, $args) {
		$this->page = SupportHandler::get_page($request->get['referrer']);
		return parent::setup($request, $args);
	}
}

class LoginView extends BubblegumView
{
	public function setup($request, $args) {
		if ($request->user->logged_in()) {
			header("Location: " . home_url);
			return false;
		}
		$setup = parent::setup($request, $args);
		$request->media->add_file(home_dir . "contrib/admin/media/css/auth.css");
		return $setup;
	}
}

class ProfileView extends BaseView
{
	public function setup($request, $args) {
		if (!parent::setup($request, $args))
			return false;
		
		$request->media->add_file(home_dir . "apps/core/media/css/dashboard.css");

		$request->media->add_file(home_dir . "apps/core/media/js/jquery.drag.js");
		$request->media->add_file(home_dir . "apps/core/media/js/jquery.utils.js");
		$request->media->add_file(home_dir . "apps/core/media/js/dashboard.js");
		
		if (isset($args['user']))
			$request->profile = User::get_or_ignore($args['user']);
		else
			$request->profile = $request->user;
		
		return true;
	}
}

class ProjectView extends BaseView
{
	public function setup($request, $args) {
		if (!parent::setup($request, $args))
			return false;
		$request->project = Project::get_or_ignore($args['project']);
		// Check we should be able to view the project
		if ($request->project && !$request->project->public && !$request->project->can_view($request->user)) {
			return false;
		}
		return true;
	}
}

class NewProjectView extends BaseView
{
	public function setup($request, $args) {
		if (!parent::setup($request, $args))
			return false;
		
		$owners = array();
		$owners["User|" . $request->user->pk] = $request->user;
		foreach (Team::mine($request->user) as $team) {
			$owners["Team|" . $team->pk] = $team;
		}
		
		$project = new Project();
		$request->project_form = new Form2($request->fullPath  . "?new_project=true");
		$request->project_form->fieldset("Create a project...")
			->append("owner", new SelectFormField("Owner: ", $owners))
			->append("name", "Name: ", "char")
			->append("description", "Description: ", "textarea")
			->append("public", "Public: ", "checked");
			
		if (isset($request->get['new_project'])) {
			try {
				$request->project_form->load_post_data($request->post);
				$project->owner = $request->user;
				$model = $request->project_form->save($project, $request);
				if ($model) {
					$request->message("Project created successfully.");
					header("Location: " . home_url . "projects/" . $model->pk . "/");
					return false;
				}
			} catch (exception $e) {
				$request->message($e->getMessage());
			}
		}
		
		return true;
	}
}

class NewTeamsView extends BaseView
{
	public function setup($request, $args) {
		if (!parent::setup($request, $args))
			return false;
		
		$request->team_form = new Form2($request->fullPath  . "?new_team=true");
		$request->team_form->fieldset("Create a team...")
			->append("name", "Name: ", "char")
			->append("description", "Description: ", "textarea");
			
		if (isset($request->get['new_team'])) {
			try {
				$team = new Team();
				$request->team_form->load_post_data($request->post);
				$team->leader = $request->user;
				$model = $request->team_form->save($team, $request);
				if ($model) {
					$request->message("Team created successfully.");
					header("Location: " . home_url . "teams/" . $model->pk . "/");
					return false;
				}
			} catch (exception $e) {
				$request->message($e->getMessage());
			}
		}
		
		return true;
	}
}

class TeamsView extends BaseView
{
	public function setup($request, $args) {
		if (!parent::setup($request, $args))
			return false;
		$request->team = Team::get_or_ignore($args['team']);
		return true;
	}
}

class Git_CommitView extends ProjectView
{
	public function setup($request, $args) {
		if (!parent::setup($request, $args))
			return false;
		
		$git = $request->project->getRepository();
		
		if (isset($args['ref'])) {
			$request->git_info = $git->log($args['ref']);
		} else {
			$request->git_info = $git->log();
		}
		
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

function sort_activity($a, $b) {
	return $a[0] == $b[0] ? 0 : ($a[0] > $b[0] ? 1 : -1);
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
			if (isset($obj->user)) {
				$activity[] = array(
					$obj->joined,
					$obj->user->get_short_display_name() . " joined ".$obj->team."!",
					home_url . ""
				);
			}
		}
		foreach ($closed_tasks as $obj) {
			if (isset($obj->completed_by)) {
				$activity[] = array(
					$obj->completed,
					$obj->completed_by->get_short_display_name() . " completed ".$obj->name."!",
					home_url . "projects/".$obj->project->pk."/"
				);
			}
		}
		foreach ($created_tasks as $obj) {
			if (isset($obj->created_by)) {
				$activity[] = array(
					$obj->created,
					$obj->created_by->get_short_display_name() . " added task ".$obj->name."!",
					home_url . "projects/".$obj->project->pk."/"
				);
			}
		}
		
		usort($activity, "sort_activity");
		
		$i = 0;
		foreach ($activity as $data) {
			if ($i >= 10)
				break;
			print '<li><a href="'.$data[2].'"><i class="icon-chevron-right"></i> '.$data[1].'</a></li>';
			$i++;
		}
		if ($i == 0)
			print '<li><i class="icon-chevron-right"></i>There has been no activity yet!</li>';
	}
}

class AJAX_MileStonesView extends JSONView
{
	public function setup($request, $args) {
		$request->dataset = array();
		$request->project = Project::get_or_ignore($args['project']);
		if (!$request->project)
			die('{"error":"Incorrect Project!"}');
		// Check we should be able to view the project
		if (!$request->project->public && !$request->project->can_view($request->user)) {
			die('{"error":"You cannot view that project!"}');
		}
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
				"pk" => $milestone->pk,
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
		// Check we should be able to view the project
		if (!$request->project->public && !$request->project->can_view($request->user)) {
			return false;
		}
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
		$request->project = isset($args['project']) ? Project::get_or_ignore($args['project']) : null;
		
		// Check we should be able to view the project
		if ($request->project && !$request->project->public && !$request->project->can_view($request->user)) {
			return false;
		}
		
		if (!$request->project) {
			$tasks = Task::objects()->order_by(array("priority"));
		} else {
			$tasks = Task::objects()->filter(array("project" => $request->project->pk))->order_by(array("priority"));
		}
		
		if (isset($request->get['milestone'])) {
			$milestone = Milestone::get_or_ignore(array("name" => html_entity_decode($request->get['milestone'])));
			if ($milestone)
				$tasks = $tasks->filter(array("milestone" => $milestone->pk));
		}
		
		if ($tasks->count() == 0) {
			die('{"error":"You have no tasks!"}');
		}

		foreach ($tasks as $task) {
			if ($task->progress >= 100 || (isset($request->get['own_tasks_only']) && !$task->assigned($request->user))) {
				continue;
			}
			
			$dataset_array = array(
				"milestone" => isset($task->milestone) ? $task->milestone->__toString() : "-",
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
			
			if (isset($request->get['own_tasks_only'])) {
				$dataset_array = array_merge(array("project" => '<a href="'.home_url.'projects/'.$task->project->pk.'/">' . $task->_project->__toString() . '</a>'), $dataset_array);
			}
			$request->dataset[] = $dataset_array;
		}
		return $request->user->logged_in();
	}
}

class AJAX_TaskAddView extends View
{
	public function setup($request, $args) {
		return $request->user->logged_in() && isset($request->post['csrf']) && $request->validate_csrf_token($request->post['csrf']);
	}
	
	public function render($request, $args) {
		print $request->get_csrf_token();
		$project = Project::get_or_ignore($args['project']);
		
		// Check we should be able to view the project
		if ($project && !$project->public && !$project->can_view($request->user)) {
			return "";
		}
		
		$task = Task::create(array(
			"project" => $project,
			"name" => $request->post['name'],
			"description" => $request->post['description'],
			"type" => $request->post['type'],
			"milestone" => $request->post['milestone'],
			"created_by" => $request->user
		));
		
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

class AJAX_TaskEditView extends View
{
	public function setup($request, $args) {
		return $request->user->logged_in() && isset($request->post['csrf']) && $request->validate_csrf_token($request->post['csrf']);
	}
	
	public function render($request, $args) {
		$project = Project::get_or_ignore($args['project']);
		
		// Check we should be able to view the project
		if (!$project || !$project->can_edit($request->user)) {
			return "Error: You cannot edit this project!";
		}
		
		print $request->get_csrf_token();
		
		$task = Task::get_or_ignore($request->post['pk']);
		if ($project && $task) {
			foreach ($request->post as $var => $val) {
				if ($task->has_field($var)) {
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
		
		// Check we should be able to view the project
		if (!$request->project->public && !$request->project->can_view($request->user)) {
			die('{"error":"You cannot view that project!"}');
		}
			
		if (isset($request->get['pk']))
			$task = Task::get_or_ignore(array("pk" => $request->get['pk']));
			
		if (isset($request->get['name']))
			$task = Task::get_or_ignore(array("name" => html_entity_decode($request->get['name'])));
			
		if (isset($task)) {
			$request->dataset[] = array(
				"pk" => $task->pk,
				"milestone" => isset($task->milestone) ? $task->milestone->pk : "-",
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
		} else {
			die('{"error":"Could not find task '.html_entity_decode($request->get['name']).'!"}');
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
		
		// Check we should be able to view the project
		if (!$request->project->public && !$request->project->can_view($request->user)) {
			die('{"error":"You cannot view that project!"}');
		}
		
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

class AJAX_ProjectTrackView extends View
{
	public function setup($request, $args) {
		$request->project = Project::get_or_ignore($args['project']);
		if (!$request->project)
			die('Incorrect Project!');

		return $request->user->logged_in();
	}
	
	public function render($request, $args) {
		// Check we should be able to track the project
		if (!$request->project->public && !$request->project->can_view($request->user)) {
			die('You cannot track that project!');
		}
		
		$request->project->track($request->user);
	}
}

class AJAX_ProjectUnTrackView extends AJAX_ProjectTrackView
{
	public function render($request, $args) {
		$request->project->untrack($request->user);
	}
}

class AJAX_ProjectGitShowView extends View
{
	public function setup($request, $args) {
		$request->project = Project::get_or_ignore($args['project']);
		if (!$request->project) {
			die('Invalid Project!');
		}
		if (!$request->project->public && !$request->project->can_view($request->user)) {
			die('You cannot view that!');
		}
		if (!isset($request->get['file'])) {
			die('No file specified!');
		}
		return $request->user->logged_in();
	}
	
	public function render($request, $args) {
		// Get the file
		$filename = $request->get['file'];
		$git = $request->project->getRepository();
		if ($git !== null) {
			$lines = $git->show($filename);
			foreach ($lines as $line) {
				print htmlentities($line) . "\n";
			}
		}
	}
}
?>

