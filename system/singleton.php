<?php
class Sys_Singleton {
    private static $instances = array();
    
    protected function __construct() {}
    protected function __clone() {}
    public function __wakeup() {}

    public final static function getInstance() {
        $cls = get_called_class();      // late-static-bound class name
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }
}