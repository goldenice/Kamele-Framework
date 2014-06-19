<?php
namespace System;

/**
 * Core class
 * Handles some basic functions, like autoloader registering and output buffering
 * 
 * @package     Kamele Framework
 * @subpackage  System
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.2
 * @final
 */
final class Core {
    /**
     * @access  private
     * @var     Events-object       Instance of the Events class
     */
    private $events;
    
    /**
     * @access  private
     * @var     Router-object       Instance of the Router class
     */
    private $router;
    
    /**
     * Constructor function, loads the system
     * 
     * @access  public
     * @return  void
     */
    public function __construct() {
        // Make sure we don't get headers erroring all over the place
        ob_start();

        // And get us a session
        session_start();
        
        // Include the router class (the rest of the classes will be autoloaded)
        require_once 'system/router.php';
        
        // Register the class autoloader
        spl_autoload_register('\System\Router::autoloader');
        
        // Register exception and error handlers
        set_exception_handler(array('\System\Exceptions', 'handleException'));
        set_error_handler(array('\System\Exceptions', 'errorToException'));
        
        // Trigger the system_start event
        $this->events = \System\Events::getInstance();
        $this->events->fireEvent('system_start');
        
        // Create router object
        $this->router = new \System\Router;
    }
    
    /**
     * Destruction function, triggers event so modules can issue a proper internal shutdown
     * 
     * @access  public
     * @return  void
     */
    public function __destruct() {
        // Trigger system_stop event
        $this->events->fireEvent('system_stop');
    }
}