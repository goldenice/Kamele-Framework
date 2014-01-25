<?php
final class Sys_Models extends Sys_Singleton {
    private $saved_models;
    
    function __construct() {
        $this->saved_models = new StdClass;             // Init empty object to save other objects in
    }
    
    final function __get($name) {
        if (!isset($this->saved_models->{$name})) {
            if (file_exists(MODELS.$name.'.php')) {
                $classname = 'Model_'.ucfirst($name);
                if (is_subclass_of($classname, 'Sys_Singleton')) {
                    $this->saved_models->{$name} = $classname::getInstance();
                }
                else {
                    $this->saved_models->{$name} = new $classname;
                }
            }
            else {
                return null;
            }
        }
        return $this->saved_models->{$name};
    }
}