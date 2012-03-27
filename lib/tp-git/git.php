<?php
/**
 * Tikapot Git Library
 * Copyright (C) 2012 James Thompson
 *
 * @author James Thompson <keiouu@gmail.com>
 */

class SecurityException extends Exception {}

class Git
{
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
	
	public function log() {
	}
}
?>
