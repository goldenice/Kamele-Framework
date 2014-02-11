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
        $this->route($_GET['url']);
    }
    
    function route($uri) {
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

        // Determine which controller to load
        if ($url[0] == 'index.php' or $url[0] == '') {
            $class = '\Application\Controller\Home';
        }
        else {
            $class = '\Application\Controller\\'.ucfirst($url[0]);
        }

        // Check if we should use a custom method
        if (sizeOf($url) > 1) {
            $method = $url[1];
        }
        else {
            $method = 'index';
        }

        // Check if we should give any other arguments
        if (sizeOf($url) > 2) {
            unset($url[0]);
            unset($url[1]);
            $arg = array_values($url);
        }
        else {
            $arg == null;
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
        
        $type = strtolower($parts[0].'\\'.$parts[1]);                   // Get type
        
        unset($parts[0]);                                               // Delete time from path
        unset($parts[1]);                                               // Same
        $dir = strtolower(implode('/', array_values($parts)));         // Glue the stuff back together
        if ($dir == '' or $dir == '/') {
            $dir = '';
        }
        else {
            $dir .= '/';
        }
        
        if ($type == 'application\controller') {
            require CONTROLLERS.$dir.strtolower($name).'.php';
        }
        elseif ($type == 'application\model') {
            require MODELS.$dir.strtolower($name).'.php';
        }
        elseif ($type == 'application\service') {
            require SERVICES.$dir.strtolower($name).'.php';
        }
        elseif ($type == 'system\\') {
            require SYSTEM.$dir.strtolower($name).'.php';
        }
        else {
            return false;
        }
        return true;
    }
}