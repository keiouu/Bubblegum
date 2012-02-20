<?php
/*
 * Tikapot's Modified Version of a Radix Data Structure
 *
 * Its called an "a" Radix due to its ability to
 * return both a value, and arguments for that value 
 * that it picks up along the way.
 *
 * Used for URL handling and regex url argument extraction
 *
 */
 
require_once(home_dir . "framework/utils.php");

class NotFoundException extends Exception {}
class InvalidTrieQueryException extends Exception {}
class ZeroQueryExcpetion extends Exception {}

class ARadix
{
	private $value, $res, $branches = array();
	
	public function __construct($val = "root", $res = NULL) {
		$this->value = $val;
		$this->res = $res;
	}
	
	public function is_root() {
		return $this->value == "root";
	}
	
	public function get() {
		return $this->value;
	}
	
	public function get_resource() {
		return $this->res;
	}
	
	public function set_resource($res) {
		$this->res = $res;
	}
	
	public function size() {
		$size = 1;
		foreach ($this->children() as $val => $child)
			$size += $child->size();
		return $size;
	}
	
	public function children() {
		return $this->branches;
	}
	
	public function add($trie) {
		$key = $trie->get();
		if (isset($this->branches[$key])) {
			foreach ($trie->children() as $child) {
				$this->branches[$key]->add($child);
			}
			if ($trie->get_resource() !== null)
				$this->branches[$key]->set_resource($trie->get_resource());
		} else {
			$this->branches[$key] = $trie;
		}
	}
	
	public function match($value) {
		if (preg_match("/" . $this->value . "/", $value, $matches))
			return $matches;
		return false;
	}
	
	public function is_regex() {
		return starts_with($this->value, "(") && ends_with($this->value, ")");
	}
	
	public function get_branch($q) {
		if (isset($this->branches[$q]))
			return $this->branches[$q];
		foreach ($this->children() as $val => $branch) {
			if ($branch->is_regex()) {
				if ($branch->match($q))
					return $branch;
			}
		}
		return null;
	}
	
	public function has_branch($q) {
		return $this->get_branch($q) !== null;
	}
	
	/*
	 * $query = array("posts", "(?P<name>\w+)");
	 * returns array(View, [args])
	 */
	public function query($query, $args = array()) {
		if (!is_array($query)) throw new InvalidTrieQueryException();
		$next = $query[0];
		
		// Six cases here:
		//    - There is no next node
		//    - This is the final node ($query has one element)
		//        - And it is a regex
		//    - There is a next node
		//        - It is hardcoded
		//        - Its a regex
		
		$branch = $this->get_branch($next);
		if ($branch === null) {
			// Case 1: There is no next node
			throw new NotFoundException();
		}
		
		if ($branch->is_regex()) {
			// Case 6: It is a regex
			$args = array_merge_recursive($args, $branch->match($next));
		}
		
		// Case 2: This is the final node
		if (count($query) == 1) {
			return array($branch->get_resource(), $args);
		}
		
		// Case 4: There is a next node
		if (count($query) > 1) {
			// Case 5: It is hardcoded
			return $branch->query(array_slice($query, 1), $args);
		}
		
		// Query somehow hit length 0
		throw new ZeroQueryExcpetion();
	}
	
	public function print_radix($depth = 0) {
		for ($i = 0; $i < $depth; $i++)
			print "\t";
		print "ARadix (";
		print $this->get();
		if ($this->res)
			print " (".$GLOBALS["i18n"]["hasres"].")";
		foreach ($this->children() as $val => $branch) {
			print "\n";
			$branch->print_radix($depth + 1);
		}
		if (count($this->children()) > 0) {
			print "\n";
			for ($i = 0; $i < $depth; $i++)
				print "\t";
		}
		print ")";
		if ($this->is_root())
			print "\n";
	}
}






?>
