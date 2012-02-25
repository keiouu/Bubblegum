<?php
/*
 * Tikapot Profiler
 *
 */

require_once(home_dir . "framework/signal_manager.php");
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/model_fields/init.php");

// TODO - should use tp 1.2 "historical models" to store data
class ProfileData
{
	public static $db_total_queries = 0, $db_queries = array();
	private $name, $start_time = 0, $end_time = 0;
	
	public function __construct($name) {
		$this->name = $name;
	}
	
	public function start() {
		$this->start_time = microtime(True);
	}
	
	public function stop() {
		$this->end_time = microtime(True);
		return $this->end_time - $this->start_time;
	}
	
	public function __toString() {
		$string = $this->name . ": ";
		if ($this->end_time === 0)
			return $string . $GLOBALS['i18n']['profiler_unclosed'];
		return $string . ($this->end_time - $this->start_time) . ' ' . $GLOBALS['i18n']['profiler_seconds'];
	}
}

function profiler_db_query_hook($dbargs) {
	list ($query, $args) = $dbargs;
	ProfileData::$db_total_queries++;
	if (!isset(ProfileData::$db_queries[$query]))
		ProfileData::$db_queries[$query] = 0;
	ProfileData::$db_queries[$query]++;
}
SignalManager::hook("on_db_query", "profiler_db_query_hook");

class Profiler
{
	private static $blocks = array();
	
	public static function start($block) {
		if (!isset(Profiler::$blocks[$block]))
			Profiler::$blocks[$block] = array();
		$block_obj = new ProfileData($block);
		$id = count(Profiler::$blocks[$block]);
		Profiler::$blocks[$block][$id] = $block_obj;
		$block_obj->start();
		return $id;
	}
	
	public static function end($block, $id = 0) {
		$block = Profiler::$blocks[$block][$id];
		return $block->stop();
	}
	
	public static function get_blocks() {
		return Profiler::$blocks;
	}
}
?>

