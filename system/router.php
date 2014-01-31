<?php
/**
 * 
 * System router
 * Loads right classes 'n stuff
 * 
 */

namespace System;

class Router {
    function __construct() {
        // Register the class autoloader
        spl_autoload_register('\System\Router::autoloader');
        
        // Split the url into several vars
        if (substr($_GET['url'], -1) == '/') {
            $url = substr($_GET['url'], 0, (strlen($_GET['url'])-1));
        }
        else {
            $url = $_GET['url'];
        }
        if (substr($_GET['url'], 0, 1) == '/') {
            $url = substr($_GET['url'], 1);
        }
        else {
            $url = $_GET['url'];
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
            $controller->$method($arg);
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
        $parts = explode('\\', $classname);
        $name = end($parts);
        
        unset($parts[count($parts)-1]);
        
        $type = strtolower(implode('\\', array_values($parts)));
        
        if ($type == 'application\controller') {
            require CONTROLLERS.strtolower($name).'.php';
        }
        elseif ($type == 'application\model') {
            require MODELS.strtolower($name).'.php';
        }
        elseif ($type == 'application\service') {
            require SERVICES.strtolower($name).'.php';
        }
        elseif ($type == 'system') {
            require SYSTEM.strtolower($name).'.php';
        }
        else {
            return false;
        }
        return true;
    }
}