<?php
namespace System;

class Events extends \System\Singleton {
    private $listeners;
    
    function fireEvent($name, &$data = null) {
        if (!empty($this->listeners[$name])) {
            foreach ($this->listeners[$name] as $k=>$v) {
                if ($data != null) {
                    call_user_func($v, $data);
                }
                else { 
                    call_user_func($v);
                }
            }
        }
        return $data;
    }
    
    function addListener($event, $function) {
        $this->listeners[$event][] = $function;
        return true;
    }
    
    function deleteListener($event, $function) {
        if (!empty($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $k=>$v) {
                if ($v == $function) {
                    unset($this->listeners[$event][$k]);
                    return true;
                }
            }
        }
        return false;
    }
}