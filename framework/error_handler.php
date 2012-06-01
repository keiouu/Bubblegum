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

    switch ($errno) {
	    case E_PARSE:
	    case E_ERROR:
	    case E_USER_ERROR:
	    		console_error("[" . $errfile . ":".$errline."] " . $errstr);
	    		// TODO: full page error
	        break;
	    
	    case E_DEPRECATED:
	    case E_WARNING:
	    case E_NOTICE:
	    case E_STRICT:
	    case E_USER_NOTICE:
	    case E_USER_WARNING:
	    		console_warning("[" . $errfile . ":".$errline."] " . $errstr);
	    	break;
	
	    default:
        	break;
    }

    return true;
}
?>