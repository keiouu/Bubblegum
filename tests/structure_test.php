<?php
/*
 * Tikapot
 *
 */
 
require_once(home_dir . "lib/simpletest/unit_tester.php");
require_once(home_dir . "framework/structures/ARadix.php");


class StructureTest extends UnitTestCase {    
	function testTrie() {
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
}

?>
