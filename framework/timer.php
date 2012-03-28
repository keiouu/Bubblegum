<?php
/*
 * Tikapot Timer Class
 *
 */

class Timer
{
	private static $current_uid = 0;
	private $uid = 0, $start_time = 0, $end_time = 0;
	
	private function __construct($microtime = NULL) {
		$this->uid = self::$current_uid;
		if ($microtime != NULL)
			$this->start_time = $microtime;
		else
			$this->start_time = microtime(True);
		self::$current_uid++;
	}
	
	public function __toString() {
		return "" . round($this->ping(), 4);
	}
	
	/* Starts a new timer and returns it */
	public static function start() {
		return new Timer();
	}
	
	/* Starts a new timer with a base time and returns it */
	public static function startAt($microtime) {
		return new Timer($microtime);
	}
	
	/* Returns the current time on the timer without ending it */
	public function ping() {
		if ($this->end_time > 0) return $this->end_time;
		return microtime(True) - $this->start_time;
	}
	
	/* Returns the current time on the timer, ending it causing future calls to ping() to return the time at the point of the stop() call. */
	public function stop() {
		$this->end_time = $this->ping();
		return $this->end_time;
	}
}

?>

