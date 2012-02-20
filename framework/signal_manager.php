<?php
/*
 * Tikapot Signal Manager
 *
 */

class SignalException extends Exception {}

class SignalManager
{
	private static $signals = array();
	
	public static function register() {
		$arg_list = func_get_args();
   	foreach ($arg_list as $signal) {
			if (isset(SignalManager::$signals[$signal]))
				throw new SignalException($GLOBALS["i18n"]["sigerr1"] . " " . $signal);
			SignalManager::$signals[$signal] = array();
		}
	}
	
	public static function hook($signal, $function, $obj = Null) {
		if (!isset(SignalManager::$signals[$signal]))
			throw new SignalException($GLOBALS["i18n"]["sigerr2"] . " " . $signal);
		SignalManager::$signals[$signal][$function] = $obj;
	}
	
	public static function fire($signal, $obj = Null) {
		if (!isset(SignalManager::$signals[$signal]))
			throw new SignalException($GLOBALS["i18n"]["sigerr2"] . " " . $signal);
		foreach (SignalManager::$signals[$signal] as $function => $object) {
			if ($object)
				if(method_exists($object, $function))
					call_user_func_array(array($object, $function), array($obj));
				else
					throw new SignalException($GLOBALS["i18n"]["sigerr3"] . " " . $function);
			else
				$function($obj);
		}
	}
}

?>

