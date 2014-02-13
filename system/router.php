<?php
/**
 * 
 * System router
 * Loads right classes 'n stuff
 * 
 */

namespace System;

final class Router {
    function __construct() {
        // Register the class autoloader
        spl_autoload_register('\System\Router::autoloader');
        
        // Fire up the actual routing
		// PHP_SELF always returns the script index.php file itself.
		$this->route($_SERVER['PHP_SELF']);
    }
    
    function route($uri) {
		$arguments = explode('index.php', $uri);
		if (isset($arguments[1])) {
			$uri = $arguments[1];
		} else {
			$uri = '/';
		}
		
        // Split the url into several vars
        if (substr($uri, -1) == '/') {
            $url = substr($uri, 0, (strlen($uri)-1));
        }
        else {
            $url = $uri;
        }
        if (substr($uri, 0, 1) == '/') {
            $url = substr($uri, 1);
        }
        else {
            $url = $uri;
        }
        $url = explode('/', trim($url));
		
        // Determine which module and controller to load
        if ($url[0] == '') {
            $class = '\Modules\Main\Controllers\Home';
        }
        else {
			// Sets the default controller to "Home" if it's not set.
			if (!isset($url[1])) {
				$url[1] = 'Home';
			}
			
            $class = '\Modules\\'.ucfirst($url[0]).'\Controllers\\'.ucfirst($url[1]);
            if (!class_exists($class, true)) {
                array_unshift($url, 'Home');
            }
			
            $class = '\Modules\\'.ucfirst($url[0]).'\Controllers\\'.ucfirst($url[1]);
        }
		

        // Check if we should use a custom method
        if (sizeOf($url) > 2) {
            $method = $url[2];
        }
        else {
            $method = 'index';
        }

        // Check if we should give any other arguments
        if (sizeOf($url) > 3) {
            unset($url[0]);
            unset($url[1]);
            unset($url[2]);
            $arg = array_values($url);
        }
        else {
            $arg = null;
        }

        // Create controller
        $controller = new $class;
        if (method_exists($controller, $method)) { 
            $controller->$method($arg);     // Actual execution of the desired controller function
        }
        else {
            // Handle nice error thingey or something
            die('Fatal error: method not found');
        }
    }
    
    static function redirect($uri) {
        header('Location: '.BASEURL.$uri);
        exit();
    }
    
    static function autoloader($classname) {
        // Separate the parts of the whole namespace
        $parts = explode('\\', $classname);
        $name = end($parts);                                            // Get classname
        
        unset($parts[count($parts)-1]);                                 // Delete classname for putting the path back together

        $dir = strtolower(implode('/', array_values($parts)));          // Glue the stuff back together
        if ($dir == '' or $dir == '/') {
            $dir = '';
        }
        else {
            $dir .= '/';
        }
        
        require strtolower($dir).strtolower($name).'.php';

        return true;
    }
}