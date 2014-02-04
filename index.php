<?php
/**
 * Kamele Framework v1.0
 * 
 * @author      Rick Lubbers            <me@ricklubbers.nl>
 * @license     Kamele License v1.0     see LICENSE.txt
 * 
 * 04 February 2014
 */

// Load config files
$configdir = 'application/config';
$cfg = opendir($configdir);
while ($item = readdir($cfg)) {
    $ext = explode('.', $item);
    if (end($ext) == 'php') {
        require($configdir.'/'.$item);
    }
}

// Make sure errors get displayed if we are in development mode
if (DEVELOPMENT == true) {  
    ini_set('display_errors', '1');
    error_reporting(E_ALL ^ E_NOTICE);
}
else {
    ini_set('display_errors', '1'); // It may seem weird, but it indeed is 1!
    error_reporting(0);
}

// Make sure we don't get headers erroring all over the place
ob_start();

// And get us an awesome session
session_start();

// Let us include the router class
require_once SYSTEM.'router.php';

// Fire up the router, and start the rest of the loading procedure.
// It's not like we're going to do more stuff in index.php
new \System\Router;