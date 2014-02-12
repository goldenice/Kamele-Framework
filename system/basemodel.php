<?php
namespace System;

abstract class Basemodel {
    protected $db;
    protected $models;
    protected $events;
 
    function __construct() {
        $this->db = \System\Database::getInstance();
        $this->models = new \System\Models;
        $this->events = \System\Events::getInstance();
    }   
}