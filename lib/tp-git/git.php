<?php
/**
 * Tikapot Git Library
 * Copyright (C) 2012 James Thompson
 *
 * @author James Thompson <keiouu@gmail.com>
 */

require_once(home_dir . "framework/utils.php");

/**
 * SecurityException
 *
 */
class SecurityException extends Exception {}

/**
 * Git
 *
 */
class Git
{
	protected /** The path to the git repository */ $_path;
	
	/**
	 * Construct a new virtual repo
	 *
	 * @param string $path The path to the repository
	 */
	public function __construct($path) {
		$this->_path = $path;
	}
	
	/**
	 * Get the latest commit log
	 *
	 * @param string $commit The commit ref to show (optional)
	 * @returns null|array Null on failure or array(hash, author, email, date, message) on success
	 */
	public function log($commit = "-1") {
		$tp_path = getcwd();
		if (!is_dir($this->_path) || !chdir($this->_path))
			return null;
		$commit = escapeshellarg($commit);
		exec('git log '.$commit.' --pretty="format:%H##%P##%ae##%an##%ad##%s"', $ret);
		$array = array();
		if (isset($ret[0])) {
			list($array["hash"], $array["parent"], $array["email"], $array["author"], $array["date"], $array["message"]) = explode("##", $ret[0]);
			$array["date"] = strtotime($array["date"]);
		}
		chdir($tp_path);
		return $array;
	}
	
	/**
	 * Returns all file changed for a given commit (git whatchanged)
	 *
	 * @param string $commit (optional) The hash to check
	 * @return null|array Null on failure or array(hash, author, email, date, message) on success
	 */
	public function files_changed($commit = "-") {
		if (!chdir($this->_path))
			return null;
		$commit = escapeshellarg($commit);
		exec('git whatchanged '.$commit.' --oneline -1', $ret);
		$ret = array_slice($ret, 1);
		$array = array();
		if (isset($ret[0])) {
			foreach ($ret as $line) {
				$elements = explode(" ", $line);
				if (isset($elements[4])) {
					$array[] = array("status" => substr($elements[4], 0, 1), "file" => substr($elements[4], 1));
				}
			}
		}
		return $array;
	}
	
	/**
	 * Returns all file changes for a given commit (git show)
	 *
	 * @param string $commit (optional) The hash to check
	 * @return null|array Null on failure or array(hash, author, email, date, message) on success
	 */
	public function file_changes($commit = "-") {
		if (!chdir($this->_path))
			return null;
		$commit = escapeshellarg($commit);
		exec('git show '.$commit.' --oneline -1', $ret);
		$lines = array_slice($ret, 1);
		
		$array = array();
		$current_file = "";
		foreach ($lines as $line) {
			if (starts_with($line, "diff") || (starts_with($line, "index") && $current_file == "")) {
				$current_file = "";
				continue;
			}
			
			if (starts_with($line, "+++") || starts_with($line, "---") ) {
				$current_file = substr($line, 6);
				if (!isset($array[$current_file]))
					$array[$current_file] = array();
				continue;
			}
			
			if ($current_file == "")
				continue;
			
			$array[$current_file][] = $line;
		}
		return $array;
	}
	
	/**
	 * This function provides a safe way to call "exec".
	 *
	 * @param string $command The command to run (e.g. ls)
	 * @param array $args The args to run with the command (e.g. ["-al"])
	 * @param int $expected_arg_count The expected number of arguments, counted by spaces (e.g. 1) or -1 to disable checking (e.g. if a string is passed)
	 */
	protected static function _exec($command, $args = array(), $expected_arg_count = -1) {
		$arg_count = 0;
		foreach ($args as $arg) {
			$arg_count += count(explode(" ", $arg));
			$command .= " " . escapeshellarg($arg);
		}
		if ($expected_arg_count !== -1 && $arg_count !== $expected_arg_count)
			throw new SecurityException("Expected arg count '".$expected_arg_count."' doesnt match real count '".$arg_count."'!");
		$command = escapeshellcmd($command);
		exec($command, $output);
		return $output;
	}
	
	/**
	 * Equivalent to "git init --bare"
	 * Also sets up our hooks
	 *
	 * @param string $dir The dir to the repo
	 * @param string $description The description of the repo
	 */
	public static function Init($dir, $description) {
		if (!ends_with($dir, "/"))
			$dir = $dir . "/";
		
		// Create and move to the git dir
		if (!file_exists($dir))
			mkdir($dir);
		chdir($dir);
		
		// Init the git dir
		$ret = Git::_exec("git init", array("--bare"));
		
		// Setup our hooks
		rmrf($dir . "hooks");
		mkdir($dir . "hooks");
		$objects = scandir(home_dir . "repo/hooks/");
		foreach ($objects as $object) {
			if ($object == "." || $object == "..")
				continue;
			copy(home_dir . "repo/hooks/" . $object, $dir . "hooks/" . $object);
		}
		
		file_put_contents($dir . "description", $description);
		
		return $ret;
	}
}
?>
