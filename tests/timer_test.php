<?php
/*
 * Tikapot
 *
 */

require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/timer.php");


class TimerTest extends UnitTestCase {
	function testTimer() {
		$timer = Timer::start();
		$this->assertTrue($timer);
		$pingtime = $timer->ping();
		$this->assertTrue($timer->ping() > 0);
		$endtime = $timer->stop();
		$this->assertTrue($endtime > $pingtime);
		for ($i=0; $i > 100; $i++){} // Time passes..
		$this->assertEqual($endtime, $timer->ping());
	}
}

?>

