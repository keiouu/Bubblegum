<?php
/*
 * Tikapot's new tests!
 */

require_once(home_dir . "lib/simpletest/unit_tester.php");

class Framework_Tests extends UnitTestCase {
	public function testUtils() {
		require_once(home_dir . "framework/utils.php");
		// Run starts_with tests
		$this->assertTrue(starts_with("abcde", "a"));
		$this->assertTrue(starts_with("abcde", "ab"));
		$this->assertFalse(starts_with("abcde", "b"));
		$this->assertFalse(starts_with("abcde", ""));
		$this->assertFalse(starts_with("", "a"));
		// Run ends_with tests
		$this->assertTrue(ends_with("abcde", "e"));
		$this->assertTrue(ends_with("abcde", "de"));
		$this->assertFalse(ends_with("abcde", "ed"));
		$this->assertFalse(ends_with("abcde", ""));
		$this->assertFalse(ends_with("", "a"));
		// Run partition tests
		$this->assertEqual(partition("hey-you", "-"), array("hey", "-", "you"));
		$this->assertEqual(partition("a-b", "-"), array("a", "-", "b"));
		$this->assertEqual(partition("-b", "-"), array("", "-", "b"));
		$this->assertEqual(partition("a-", "-"), array("a", "-", ""));
		$this->assertEqual(partition("a", "-"), array("a", "-", ""));
		// Run get_named_class tests
		$this->assertEqual(get_class(get_named_class("Config")), "Config"); 
		$this->assertEqual(get_named_class("hbbvisvsuvis"), null);
		// Run get_file_extension tests
		$this->assertEqual(get_file_extension("hey.png"), "png");
		$this->assertEqual(get_file_extension(".png"), "png");
		$this->assertEqual(get_file_extension("hey"), "");
		$this->assertEqual(get_file_extension("hey."), "");
		// Run get_file_name tests
		$this->assertEqual(get_file_name("hey.png"), "hey");
		$this->assertEqual(get_file_name(".png"), "");
		$this->assertEqual(get_file_name("hey"), "hey");
		$this->assertEqual(get_file_name("hey."), "hey");
		$this->assertEqual(get_file_name("/../../hey.jpg"), "hey");
		// Test prettify
		$this->assertEqual(prettify("Hey"), "Hey");
		$this->assertEqual(prettify("HeyThere"), "Hey There");
		$this->assertEqual(prettify("Hey_there"), "Hey There");
		$this->assertEqual(prettify("Hey_There"), "Hey There");
		$this->assertEqual(prettify("HeyT"), "Hey T");
		// Test ellipsize
		$this->assertEqual(ellipsize("Hey there, how are you?", 8), "Hey...");
		$this->assertEqual(ellipsize("Hey there, how are you?", 20), "Hey there, how...");
		$this->assertEqual(ellipsize("Heythere", 7), "Heyt...");
		$this->assertEqual(ellipsize("Hey there", 10), "Hey there");
	}
	
	public function testTimer() {
		require_once(home_dir . "framework/timer.php");
		$timer = Timer::start();
		$this->assertTrue($timer);
		$pingtime = $timer->ping();
		$this->assertTrue($timer->ping() > 0);
		$endtime = $timer->stop();
		$this->assertTrue($endtime > $pingtime);
		time_sleep_until(microtime(true) + 0.1);
		$this->assertEqual($endtime, $timer->ping());
	}
	
	function testRequest() {
		require_once(home_dir . "framework/request.php");
		$req = new Request();
		$this->assertEqual($req->get_mime_type("/notafile/"), "text/html");
		$this->assertEqual($req->get_mime_type(home_dir . "tests/randoms/test_mime.txt"), "text/plain");
		$this->assertEqual($req->get_mime_type(home_dir . "tests/randoms/test_mime.css"), "text/css");
		$token1 = $req->get_csrf_token();
		$token2 = $req->get_csrf_token();
		$token3 = $req->get_csrf_token();
		$this->assertTrue($req->validate_csrf_token($token2));
		$this->assertTrue($req->validate_csrf_token($token1));
		$this->assertTrue($req->validate_csrf_token($token3));
		$this->assertEqual($req->create_url("test", "a=b", "?c=3&g=2"), "test?a=b&c=3&g=2");
		$this->assertEqual($req->create_url("test", "a=b", "?a=3&g=2"), "test?a=3&g=2");
	}
	
	function testStructure() {
		require_once(home_dir . "framework/structures/ARadix.php");
		$trie = new ARadix();
		$this->assertFalse($trie->is_regex());
		$this->assertEqual(count($trie->children()), 0);
		$this->assertEqual($trie->size(), 1);
		$addition = new ARadix("posts");
		$rex = new ARadix("(?P<name>\w+)", "299");
		$this->assertTrue($rex->is_regex());
		$addition->add($rex);
		$trie->add($addition);
		$this->assertEqual($trie->size(), 3);
		$addition->add(new ARadix("Test", "100"));
		$trie->add($addition);
		$this->assertEqual($trie->size(), 4);
		$url = array("posts", "Test");
		$this->assertEqual($trie->query($url), array("100", array()));
		$url = array("posts", "sdd");
		$result = $trie->query($url);
		$this->assertEqual($result[0], "299");
		$this->assertTrue(count($result), 2);
		if (count($result) == 2) {
			$this->assertTrue(array_key_exists("name", $result[1]));
			if (array_key_exists("name", $result[1]))
				$this->assertEqual($result[1]["name"], "sdd");
		}
	}
	
	public function print_hello() {
		print 'hello';
	}
	
	public function testSignal() {
		require_once(home_dir . "framework/signal_manager.php");
		$signalManager = new SignalManager();
		$signalManager->register("test");
		$signalManager->hook("test", "print_hello", $this);
		ob_start();
		$signalManager->fire("test");
		$this->assertEqual(ob_get_clean(), "hello");
	}
	
	public function testSession() {
		require_once(home_dir . "framework/session.php");
		$old_session = $_SESSION;
		Session::delete("Test");
		$this->assertEqual(Session::get("Test"), NULL);
		
		$new = Session::store("Test", 2);
		$this->assertEqual(Session::get("Test"), 2);
		$this->assertEqual($new, Session::get("Test"));
		
		$old = Session::store("Test", 5);
		$this->assertEqual(Session::get("Test"), 5);
		$this->assertEqual($old, $new);
		$no = Session::put("Test", 6);
		$this->assertEqual($no, False);
		$this->assertEqual(Session::get("Test"), 5);
		$this->assertTrue(Session::get("b43542y2") == NULL);
		Session::delete("Test");
		$this->assertEqual(Session::get("Test"), NULL);
		$_SESSION = $old_session;
	}
	
	public function testPostgresql() {
		require_once(home_dir . "framework/database.php");
		require_once(home_dir . "framework/model.php");
		require_once(home_dir . "framework/models.php");
		$model = new BlankModel();
		$model->set_table_name("TestPSQL");
		$model->create_table();
		$db = Database::create($model->get_db());
		if ($db->get_type() !== "psql")
			return;
		
		$this->assertTrue($model->table_exists());
		$this->assertEqual($db->get_columns($model->get_table_name()), array("id" => "int8"));
		$db->drop_table($model->get_table_name());
		$this->assertFalse($model->table_exists());
	}
	
	public function testModel() {
		require_once(home_dir . "framework/database.php");
		require_once(home_dir . "framework/model.php");
		require_once(home_dir . "framework/models.php");
		$model = new BlankModel();
		$model->set_table_name("TestModels");
		$this->assertEqual($model->get_table_name(), "TestModels");
		$model->create_table();
		$this->assertTrue($model->table_exists());
		
		// Tests
		
		// Cleanup
		$db = Database::create($model->get_db());
		$db->drop_table($model->get_table_name());
	}
}
?>
