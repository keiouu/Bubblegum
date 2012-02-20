<?php
/*
 * Tikapot
 *
 */

require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/signal_manager.php");

class TestException extends Exception{}

function on_testfire($obj) {
	throw new TestException("it works");
}

class SigTest2 {
	function on_test2($obj) {
		throw new TestException("it works 2");
	}
}

class SigTest3 {
	function on_test3($obj) {
		throw new TestException($obj->exception);
	}
}

class SigTestExcept { public $exception = "it works 3"; }

class SignalTest extends UnitTestCase {
	function testSignal() {
		$signalManager = new SignalManager();
		$signalManager->register("test");
		$signalManager->hook("test", "on_testfire");
		
		$this->expectException( new TestException("it works") );
		$signalManager->fire("test");
	}
	
	function testSignal2() {
		$signalManager = new SignalManager();
		$signalManager->register("test2");
		$signalManager->hook("test2", "on_test2", new SigTest2());
		
		$this->expectException( new TestException("it works 2") );
		$signalManager->fire("test2");
	}
	
	function testSignal3() {
		$signalManager = new SignalManager();
		$signalManager->register("test3");
		$signalManager->hook("test3", "on_test3", new SigTest3());
		
		$this->expectException( new TestException("it works 3") );
		$signalManager->fire("test3", new SigTestExcept());
	}
}

?>

