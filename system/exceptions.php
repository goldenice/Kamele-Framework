<?php
namespace System;
if (!defined('SYSTEM')) exit('No direct script access allowed');

/**
 * Exception class, handles everything with errors and exceptions
 * 
 * @package     Kamele Framework
 * @subpackage  System
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.2
 */
class Exceptions extends Singleton {
    
    /**
     * Handles exceptions
     * 
     * @access  public
     * @param   array       $e          Exception data
     * @return  void
     */
    static public function handleException($e) {
        $event = Events::getInstance();
        $event->fireEvent('exception_caught', $e);
        if (!empty($e)) {
            echo "\n<br />".'Exception: '.$e->getMessage();
            echo "\n<br /> In".$e->getFile().' on line '.$e->getLine();
        }
    }
    
    /**
     * Converts errors into exceptions
     * 
     * @access  public
     * @param   int         $num        Level of the error
     * @param   string      $str        Error message
     * @param   string      $file       The file the error occurred in
     * @param   int         $line       Linenumber the error occurred
     * @param   array       $context    Contains array of every variable in the scope where the error was triggered
     * @return  boolean
     */
    static function errorToException($num, $str, $file, $line, $context = null) {
        throw new \ErrorException($str, 0, $num, $file, $line);
        return true;
    }
}