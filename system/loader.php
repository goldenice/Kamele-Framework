<?php
namespace System;

/**
 * Class that handles loading and saving of classes
 * 
 * @package     Kamele Framework
 * @subpackage  System
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.0-beta
 * @final
 */
final class Loader extends Singleton implements \ArrayAccess {
    /**
     * @access  private
     * @var     array       Array of saved objects
     */
    private $saved = array();
    
    /**
     * Unimplemented function, has to be implemented because of interface
     * 
     * @access  public
     * @param   string|int  $offset     The array-key
     * @param   ?           $value      Value to set
     * @return  boolean
     * @finall
     */
    final public function offsetSet($offset, $value) {
        return false; 
    }
    
    /**
     * Check if a certain class has already been saved in this object
     * 
     * @access  public
     * @param   string|int  $offset     The classname
     * @return  boolean
     */
    final function offsetExists($offset) {
        return isset($this->saved[$offset]);
    }
    
    /**
     * Unload a specific object
     * 
     * @access  public
     * @param   string|int  $offset     The classname of the object to unset
     * @return  void
     * @final
     */
    final function offsetUnset($offset) {
        unset($this->saved[$offset]);
    }
    
    /**
     * Get an object from saved array. If it is not saved, then create it
     * 
     * @access  public
     * @param   string      $name       The name of the class to load
     * @throws  Exception   If the class couldn't be found
     * @return  Object|null
     * @final
     */
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