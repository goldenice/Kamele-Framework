<?php
final class Sys_Services extends Sys_Singleton {
    private $saved_services;
    
    function __construct() {
        $this->saved_services = new StdClass;               // Init empty object to save other objects in
    }
    
    final function __get($name) {
        if (!isset($this->saved_services->{$name})) {
            if (file_exists(MODELS.$name.'.php')) {
                $classname = 'Service_'.ucfirst($name);
                if (is_subclass_of($classname, 'Sys_Singleton')) {
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