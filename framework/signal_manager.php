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
				throw new SignalException($GLOBALS['i18n']['framework']["sigerr1"] . " " . $signal);
			SignalManager::$signals[$signal] = array();
		}
	}
	
	public static function hook($signal, $function, $obj = Null, $weighting = 50) {
		if (!isset(SignalManager::$signals[$signal]))
			throw new SignalException($GLOBALS['i18n']['framework']["sigerr2"] . " " . $signal);
			
		SignalManager::$signals[$signal][] = array($obj, $function, $weighting);
	}
	
	public static function fire($signal, $obj = Null) {
		if (!isset(SignalManager::$signals[$signal]))
			throw new SignalException($GLOBALS['i18n']['framework']["sigerr2"] . " " . $signal);
		
		// Sort array by weighting
		usort(SignalManager::$signals[$signal], create_function('$a,$b', 'return $a[2] < $b[2];'));
		
		foreach (SignalManager::$signals[$signal] as $array) {
			list($object, $function, $weighting) = $array;
			if ($object)
				if(method_exists($object, $function))
					call_user_func_array(array($object, $function), array($obj));
				else
					throw new SignalException($GLOBALS['i18n']['framework']["sigerr3"] . " " . $function);
			else
				$function($obj);
		}
	}
}

?>

