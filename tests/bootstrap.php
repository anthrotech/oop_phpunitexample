<?php

ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// PHPUnit dies silently with FATAL ERRORS which makes it hard to debug the tests.
register_shutdown_function('PHPUnit_shutdownFunction');
function PHPUnit_shutdownFunction() {
	// http://www.php.net/manual/en/errorfunc.constants.php
	$error = error_get_last();
	if (!is_null($error)){
		if($error['type'] & (E_ERROR + E_PARSE + E_CORE_ERROR + E_COMPILE_ERROR + E_USER_ERROR + E_RECOVERABLE_ERROR)){
			echo 'Test Bootstrap: Caught untrapped fatal error: ';
			var_export($error);
		}
	}
}
?>