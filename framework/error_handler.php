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
	    case E_ERROR: // TODO: full page error
	    case E_PARSE:
	    		console_error("[" . $errfile . ":".$errline."] " . $errstr);
	        break;
	    
	    case E_WARNING:
	    case E_NOTICE:
	    case E_STRICT:
	    		console_warning("[" . $errfile . ":".$errline."] " . $errstr);
	    	break;
	        
	    case E_USER_ERROR:
	        exit(1);
	        break;
	
	    case E_USER_WARNING:
	        break;
	
	    case E_USER_NOTICE:
	        break;
	
	    default:
        	break;
    }

    return true;
}
?>