<?php
/*
 * Tikapot Big Integer Field
 *
 */

require_once(home_dir . "framework/model_fields/intfield.php");

class BigIntField extends IntegerField
{
	protected static $db_type = "BIGINT";
}

?>

