<?php
/**
 * Tikapot Big Integer Field
 *
 * @author James Thompson
 * @package Tikapot\Framework
 */

require_once(home_dir . "framework/model_fields/intfield.php");

/**
 * Big Integer Field
 *
 * @package Tikapot\Framework
 */
class BigIntField extends IntegerField
{
	protected static /** Database Type */ $db_type = "BIGINT";
}

?>

