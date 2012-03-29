<?php
/**
 * Tikapot Git Library
 * Copyright (C) 2012 James Thompson
 *
 * @author James Thompson <keiouu@gmail.com>
 */

require_once(home_dir . "framework/utils.php");

class SecurityException extends Exception {}

class Git
{
	protected $_path;
	
	/**
	 * Construct a new virtual repo
	 *
	 * @param string $path The path to the repository
	 */
	public function __construct($path) {
		$this->_path = $path;
	}
	
	/**
	 * Get the latest (n) commit logs
	 *
	 * @param string $count The number of logs to retrieve
	 * @returns null|array Null on failure or array(commit, author, date, message) on success
	 */
	public function log($count = 1) {
		if (!chdir($this->_path))
			return null;
		$ret = Git::_exec("git log", array("-n " . $count));
		$array = array();
		list($ignore, $array["commit"]) = explode(" ", $ret[0]);
		list($ignore, $array["author"]) = explode(" ", $ret[1], 2);
		list($ignore, $array["date"]) = explode(" ", $ret[2], 2);
		$array["message"] = trim($ret[4]);
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
		exec($command, &$output);
		return $output;
	}
	
	/**
	 * Equivalent to "git init --bare"
	 * Also sets up our hooks
	 *
	 * @param string $dir The dir to the repo
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
