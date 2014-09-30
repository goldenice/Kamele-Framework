<?php
namespace System;
if (!defined('SYSTEM')) exit('No direct script access allowed');

/**
 * If a class extends this class, only one instance can exist of that class at any given time
 * 
 * @package     Kamele Framework
 * @subpackage  System
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.0-alpha
 */
class Singleton {
    /**
     * @access  private
     * @var     array       Array of instances
     */
    private static $instances = array();
    
    /**
     * Some default functions we need to disable!
     */
    protected function __construct() {}
    protected function __clone() {}
    public function __wakeup() {}

    /**
     * Creates 'new' instance of the class
     * 
     * @access  public
     * @return  Object
     * @final
     */ 
    public final static function getInstance() {
        $cls = get_called_class();      // late-static-bound class name
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }
}