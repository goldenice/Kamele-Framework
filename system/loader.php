<?php
namespace System;

final class Loader extends Singleton implements \ArrayAccess {
    private $saved = array();
    
    final function offsetSet($offset, $value) {
        return false;   // Because interface this function needed to be implemented, but doesn't do anything
    }
    
    final function offsetExists($offset) {
        return isset($this->saved[$offset]);
    }
    
    final function offsetUnset($offset) {
        unset($this->saved[$offset]);
    }
    
    final function offsetGet($name) {
        if (!isset($this->saved[$name])) {
            if (class_exists($name)) {
                if (is_subclass_of($name, '\System\Singleton')) {
                    $this->saved[$name] = $name::getInstance();
                }
                else {
                    $this->saved[$name] = new $name;
                }
            }
            else {
                throw new Exception('Could not find class '.$name);
                return null;
            }
        }
        return $this->saved[$name];
    }
}