<?php
/**
 * Kamele Framework v1.0
 * 
 * @author      Rick Lubbers            <me@ricklubbers.nl>
 * @license     Kamele License v1.0     see LICENSE.txt
 * 
 * 04 February 2014
 */

// Define the run mode.
define('MODE', 'development');

if (defined('MODE')) {
	switch (MODE) {
		case 'development':
			error_reporting(E_ALL);
		break;
	
		case 'testing':
		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}
}

// Define the config directory.
$configdir = 'config';

$cfg = opendir($configdir);
while ($item = readdir($cfg)) {
    $ext = explode('.', $item);
    if (end($ext) == 'php') {
        require($configdir.'/'.$item);
    }
}

// Make sure we don't get headers erroring all over the place
ob_start();

// And get us an awesome session
session_start();

// Let us include the router class
require_once 'system/router.php';

// Fire up the router, and start the rest of the loading procedure.
// It's not like we're going to do more stuff in index.php
new \System\Router;