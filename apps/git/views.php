<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir. "apps/core/models.php");
require_once(home_dir. "apps/git/models.php");

class GitAPIView extends View
{
	protected $request = null;
	
	public function setup($request, $args) {
		$this->request = $request;
		return PHP_SAPI === 'cli'; // Only allow command line access
	}
	
	public function render($request, $args) {
		$string = "Unhandled request (".$request->fullPath."):";
		foreach ($request->cmd_args as $name => $val) {
			$string .= " " . $name . "=" . $val;
		}
		Log::create(array("content" => $string));
	}
	
	protected function _getProject() {
		if (!$this->request)
			return null;
		$project = get_file_extension($this->request->cmd_args["cwd"], "/");
		return Project::get(array("pk" => $project));
	}
}

class GitCommitReceiveView extends GitAPIView
{
	//public function render($request, $args) {
	//	Log::create(array("content" => implode(" and ", $request->cmd_args)));
	//}
}

class GitBranchCreateView extends GitAPIView
{
	public function render($request, $args) {
		$name = get_file_extension($request->cmd_args["refname"], "/");
		Branch::create(array("project" => $this->_getProject(), "name" => $name));
	}
}

class GitBranchDeleteView extends GitAPIView
{
	public function render($request, $args) {
		$name = get_file_extension($request->cmd_args["refname"], "/");
		$branch = Branch::get_or_ignore(array("project" => $this->_getProject(), "name" => $name));
		if ($branch)
			$branch->delete();
	}
}
?>

