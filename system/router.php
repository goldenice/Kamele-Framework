<?php
/**
 * 
 * System router
 * Loads right classes 'n stuff
 * 
 */

namespace System;

final class Router {
    private $mode;      // CLI or browser
    private $events;    // The \System\Events object
    
    function __construct() {
        // Register the class autoloader
        spl_autoload_register('\System\Router::autoloader');
        
        // Check for the correct execution mode
        if (PHP_SAPI == 'cli') {
            $this->mode = 'cli';
        }
        else {
            $this->mode = 'browser';
        }
        
        // Trigger the system_start event
        $this->events = \System\Events::getInstance();
        $this->events->fireEvent('system_start');
        
        // Fire up the actual routing
        // If called with CLI, use argument 1 for routing, or else
        // we use REQUEST_URI, which returns the correct route, with the $1 here: example.com/index.php/$1
		if ($this->mode == 'cli') {
            if (isset($_SERVER['argv'][1])) {
    	        $this->route($_SERVER['argv'][0].'/'.$_SERVER['argv'][1]);    
            }
            else {
                $this->route($_SERVER['argv'][0]);      // Default main/home/index() route
            }
		}
        else {
            $this->route($_SERVER['REQUEST_URI']);
        }
        
        $this->events->fireEvent('system_stop');
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
		// no controller, module or method, defaults to main\home
        if ($url[0] == '') {
            $class = '\Modules\Main\Controllers\Home';
        }
        else {
			// Sets the default controller to "Home" if it's not set.
			if (!isset($url[1])) { 
                $url[1] = 'Home';
			}
            $class = '\Modules\\'.ucfirst($url[0]).'\Controllers\\'.ucfirst($url[1]);
        }
        
        // Fire router: class determined event
        $this->events->fireEvent('router_class_determined', $class);

        // Check if we should use a custom method
        if (sizeOf($url) > 2) {
            $method = $url[2];
        }
        else {
            $method = 'index';
        }
        
        // Fire router: method determined event
        $this->events->fireEvent('router_method_determined', $method);
        
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
        
        // Fire router: arguments determined event
        $this->events->fireEvent('router_args_determined', $arg);
        
        // Create controller
        $controller = new $class;
        if (method_exists($controller, $method)) { 
            $this->events->fireEvent('router_pre_controller');
            $controller->$method($arg);     // Actual execution of the desired controller function
            $this->events->fireEvent('router_post_controller');
        }
        else {
            // Handle nice error thingey or something
            $this->events->fireEvent('router_invalid_method');
            die('Fatal error: method not found.');
        }
    }
    
    static function redirect($uri, $statuscode = 307) {
        $statusstr = array(301 => 'Moved Permanently', 302 => 'Found', 307 => 'Temporary Redirect');
        if (isset($statusstr[$statuscode])) {
            header('HTTP/1.1 '.$statuscode.' '.$statusstr[$statuscode]);
        }
        header('Location: '.BASEURL.$uri);
        exit(0);
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