<?php
/*
 * Tikapot Key Field
 *
 */

require_once(home_dir . "framework/model_fields/bigintfield.php");

class KeyField extends BigIntField
{
	/* Is this a pk field?. */
	public function is_pk_field() { return true; }
}

?>

