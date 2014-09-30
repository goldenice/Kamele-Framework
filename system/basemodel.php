<?php
namespace System;
if (!defined('SYSTEM')) exit('No direct script access allowed');

abstract class Basemodel {
    protected $db;
    protected $loader;
    protected $events;
 
    function __construct() {
        $this->db = \System\Database::getInstance();
        $this->loader = \System\Loader::getInstance();
        $this->events = \System\Events::getInstance();
    }   
}