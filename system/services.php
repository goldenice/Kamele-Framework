<?php
namespace System;

final class Services extends Singleton {
    private $saved_services;
    
    function __construct() {
        $this->saved_services = new \StdClass;               // Init empty object to save other objects in
    }
    
    final function __get($name) {
        $name = strtolower($name);
        if (!isset($this->saved_services->{$name})) {
            if (file_exists(SERVICES.$name.'.php')) {
                $classname = '\Application\Service\\'.ucfirst($name);
                if (is_subclass_of($classname, '\System\Singleton')) {
                    $this->saved_services->{$name} = $classname::getInstance();
                }
                else {
                    $this->saved_services->{$name} = new $classname;
                }
            }
            else {
                return null;
            }
        }
        return $this->saved_services->{$name};
    }
}