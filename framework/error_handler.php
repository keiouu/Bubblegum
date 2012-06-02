<?php
require_once(home_dir . "framework/utils.php");

/**
 * Tikapot error handler
 * @internal
 */
function tpErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        return;
    }

    // TODO - separate these out better
    // TODO - add hidden stack traces?
    switch ($errno) {
	    case E_PARSE:
	    case E_ERROR:
	    case E_USER_ERROR:
	    case E_COMPILE_ERROR:
	    case E_CORE_ERROR:
	    		console_error(htmlentities("[" . $errfile . ":".$errline."] " . $errstr));
	    		// TODO: full page error
	        break;
	    
	    case E_COMPILE_WARNING:
	    case E_NOTICE:
	    case E_WARNING:
	    case E_DEPRECATED:
	    case E_STRICT:
	    case E_RECOVERABLE_ERROR:
	    case E_USER_NOTICE:
	    case E_USER_WARNING:
	    case E_USER_DEPRECATED:
	    		console_warning(htmlentities("[" . $errfile . ":".$errline."] " . $errstr));
	    	break;
	
	    default:
        	break;
    }

    return true;
}
?>