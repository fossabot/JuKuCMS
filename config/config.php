<?php

/**
 * configuration file
 */

//should a HTML comment "<!-- page was generated in xxx seconds -->" be added to every page?
define('ACTIVATE_BENCHMARK', true);

define('OPTION_PRELOAD_CLASSES', true);

define('DEBUG_MODE', true);

//show all errors
error_reporting(E_ALL);

$config = array(
	'PHP_BENCHMARK' => true
);

?>
