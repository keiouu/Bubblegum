<?php
/*
 * Tikapot
 *
 */

$cmd = False;
if(!defined("home_dir")) {
	ini_set('display_errors', '1');

	define("home_dir", dirname(__FILE__) . '/../');

	require_once(home_dir . "config.php");
	$cmd = True;
}

require_once(home_dir . "lib/simpletest/simpletest.php");

class AllTests extends TestSuite {
    function __construct() {
        parent::__construct('All tests');
        $this->addFile(home_dir . 'tests/session_test.php');
        $this->addFile(home_dir . 'tests/database_test.php');
        $this->addFile(home_dir . 'tests/timer_test.php');
        $this->addFile(home_dir . 'tests/model_test.php');
        $this->addFile(home_dir . 'tests/model_table_test.php');
        $this->addFile(home_dir . 'tests/modelquery_test.php');
        $this->addFile(home_dir . 'tests/model_field_tests.php');
        $this->addFile(home_dir . 'tests/model_inclusion_tests.php');
        $this->addFile(home_dir . 'tests/request_test.php');
        $this->addFile(home_dir . 'tests/signal_test.php');
        $this->addFile(home_dir . 'tests/auth_test.php');
        $this->addFile(home_dir . 'tests/structure_test.php');
        $this->addFile(home_dir . 'tests/ct_test.php');
    }
}

if ($cmd) {
	$tests = new AllTests();
	$tests->run(new TextReporter());
}
?>

