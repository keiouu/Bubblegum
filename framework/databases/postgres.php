<?php
/*
 * Tikapot PostgreSQL Database Extension Class
 * v1.0
 *
 */

require_once(home_dir . "framework/database.php");

class PostgreSQL extends Database
{
	private $_dbname;

	protected function connect($database) {
		global $databases;
		$settings = $databases[$database];
		$portStr = $settings['port'];
		if (strlen($portStr) > 0)
			$portStr = " port=" . $portStr;
		$this->_dbname = $settings['name'];
		$connect_str = "host=" . $settings['host'].$portStr;
		$connect_str .= " user=" . $settings['username'];
		$connect_str .= " password=" . $settings['password'];
		$connect_str .= " dbname=" . $settings['name'];
		$connect_str .= " connect_timeout=" . $settings['timeout'];
		$this->_link = pg_connect($connect_str);
		$this->_connected = $this->_link ? true : false;
		if (!$this->_connected)
			throw new NotConnectedException($GLOBALS["i18n"]["dberr1"]);
		return $this->_connected;
	}
	
	private function throw_query_exception($e) {
		throw new QueryException($e);
	}
	
	public function query($query, $args=array()) {
		if (debug_show_queries)
			print $query . "\n";
		if (!$this->_connected) {
			throw new NotConnectedException($GLOBALS["i18n"]["dberr2"]);
		}
		$res = pg_query_params($this->_link, $query, $args);
		if (strpos($query, "ATE TABLE") > 0 || strpos($query, "OP TABLE") > 0)
			$this->populate_tables();
		return $res;
	}
	
	public function fetch($result) {
		if (!$this->_connected) {
			throw new NotConnectedException($GLOBALS["i18n"]["dberr2"]);
		}
		return pg_fetch_array($result, NULL, PGSQL_BOTH);
	}
	
	public function disconnect() {
		if ($this->_connected) {
			$this->_connected = !pg_close($this->_link);
		}
	}
	
	public function populate_tables() {
		$this->_tables = array();
		$query = $this->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public';");
		while($result = $this->fetch($query))
			array_push($this->_tables, $result["table_name"]);
	}

	/* Returns a query */
	public function get_columns($table) {
		$arr = array();
		$query = $this->query("SELECT * from ".$table.";");
		$i = pg_num_fields($query);
		for ($j = 0; $j < $i; $j++)
			$arr[pg_field_name($query, $j)] = pg_field_type($query, $j);
		return $arr;
	}
	
	public function escape_string($value) {
		return pg_escape_string($this->_link, $value);
	}
}

?>

