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
		$tp_path = getcwd();
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
		chdir($tp_path);
		return $array;
	}
	
	/**
	 * Returns all file changes for a given commit (git show)
	 *
	 * @param string $commit (optional) The hash to check
	 * @return null|array Null on failure or array(hash, author, email, date, message) on success
	 */
	public function file_changes($commit = "-") {
		$tp_path = getcwd();
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
		chdir($tp_path);
		return $array;
	}
	
	/**
	 * Returns a full file listing for a given commit (or HEAD)
	 * 
	 * @param string $commit (optional) The ref to use
	 * @return Array A list of files and folders
	 */
	public function ls($commit = "HEAD") {
		$tp_path = getcwd();
		if (!chdir($this->_path))
			return null;
		$commit = escapeshellarg($commit);
		exec('git ls-tree --name-only -r '.$commit, $lines);
		$listing = array();
		foreach ($lines as $line) {
			$parts = explode("/", $line);
			$section = &$listing;
			$i = 0;
			foreach ($parts as $part) {
				if ($i === count($parts) - 1) {
					$section[] = $part; // Its the filename!
				} else {
					if (!isset($section[$part])) {
						$section[$part] = array();
					}
					$section = &$section[$part];
				}
				$i++;
			}
		}
		chdir($tp_path);
		return $listing;
	}
	
	/**
	 * Returns the contents of a specific file
	 */
	public function show($filename, $commit = "HEAD") {
		$tp_path = getcwd();
		if (!chdir($this->_path))
			return null;
		$filename = escapeshellarg($filename);
		$commit = escapeshellarg($commit);
		exec('git show ' . $commit . ":" . $filename, $lines);
		chdir($tp_path);
		return $lines;
	}
	
	/**
	 * Generate an SSH key for this project
	 * 
	 * @return String Filename for the key
	 */
	public function genKey() {
		$tp_path = getcwd();
		$path = $this->_path . "keys/";
		if (!file_exists($path))
			mkdir($path);
		if (!chdir($path))
			return null;
		
		// Exec a keygen
		$filename = realpath($path) . uniqid() . "_key.rsa";
		exec('ssh-keygen -t rsa -N "" -f ' . escapeshellarg($filename));
		
		chdir($tp_path);
		
		return $filename;
	}
	
	/**
	 * Get the branches for this repo
	 */
	public function branches() {
		$tp_path = getcwd();
		if (!chdir($this->_path))
			return null;
		
		$list = array();
		exec('git branch --list', $out);
		foreach ($out as $branch) {
			$list[] = htmlentities(trim(substr($branch, 1)));
		}
		
		chdir($tp_path);
		return $list;
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
		$tp_path = getcwd();
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
		mkdir($dir . "keys");
		$objects = scandir(home_dir . "repo/hooks/");
		foreach ($objects as $object) {
			if ($object == "." || $object == "..")
				continue;
			copy(home_dir . "repo/hooks/" . $object, $dir . "hooks/" . $object);
		}
		
		file_put_contents($dir . "description", $description);
		
		chdir($tp_path);
		return $ret;
	}
}
?>
