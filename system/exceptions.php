<?php
namespace System;

class Exceptions extends \System\Singleton {
    static function handleException($e) {
        $event = \System\Events::getInstance();
        $event->fireEvent('exception_caught', $e);
        if (!empty($e)) {
            echo "\n<br />".'Exception: '.$e->getMessage();
            echo "\n<br /> In".$e->getFile().' on line '.$e->getLine();
        }
    }
    
    static function errorToException($num, $str, $file, $line, $context = null) {
        throw new \ErrorException($str, 0, $num, $file, $line);
    }
}