<?php
/*
 * Tikapot Primary Key Field
 *
 */

require_once(home_dir . "framework/model_fields/bigintfield.php");

class PKField extends BigIntField
{
	public function db_create_query($db, $name, $table_name) {
		$val = parent::db_create_query($db, $name, $table_name);
		if ($db->get_type() == "mysql")
			$val .= " PRIMARY KEY";
		return $val;
	}
	
	public function get_formfield($name) {
		return new HiddenFormField($name, $this->get_value());
	}
	
	/* This allows subclasses to provide end-of-statement additions such as constraints */
	public function db_post_create_query($db, $name, $table_name) {
		if ($db->get_type() == "psql")
			return "CONSTRAINT ".$table_name."_pkey PRIMARY KEY (".$name.")";
	}
	
	/* Is this a pk field?. */
	public function is_pk_field() { return true; }
}

?>

