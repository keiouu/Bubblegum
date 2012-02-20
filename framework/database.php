<?php
/*
 * Tikapot Database Class
 *
 */

include_once(home_dir . "framework/databases/mysql.php");
include_once(home_dir . "framework/databases/postgres.php");

class NotConnectedException extends Exception { }
class QueryException extends Exception { }

abstract class Database
{
	private static $dbs = array();
	protected $_link, $_connected, $_tables, $_type;
	
	public static function create($database = "default") {
		if (isset(Database::$dbs[$database]))
			return Database::$dbs[$database];

		global $databases;
		$database_type = $databases[$database]['type'];
		switch ($database_type) {
			case "mysql":
				Database::$dbs[$database] = new MySQL();
				break;
			case "psql":
				Database::$dbs[$database] = new PostgreSQL();
				break;
		}
		if (Database::$dbs[$database]) {
			if (!Database::$dbs[$database]->connect($database))
				return false;
			Database::$dbs[$database]->populate_tables();
			Database::$dbs[$database]->_type = $database_type;
		}
		return Database::$dbs[$database];
	}
	
	public function is_connected() { return $this->_connected; }
	public function get_link() { return $this->_link; }
	public function get_tables() { return $this->_tables; }
	public function get_type() { return $this->_type; }
	
	protected abstract function connect($database);
	public abstract function query($query, $args=array());
	public abstract function fetch($result);
	public abstract function disconnect();
	public abstract function populate_tables();
	public abstract function escape_string($value);
	
	/* Must return:
	 * array("col"=>"type", etc);
	 */
	public abstract function get_columns($table);
	
	function __destruct() {
		$this->disconnect();
	}
}

?>

