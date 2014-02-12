<?php
namespace System;

abstract class Baseservice {
	protected $db;
    protected $models;
    protected $services;
    protected $events;
    
    function __construct() {
    	$this->db = \System\Database::getInstance();
    	$this->models = new \System\Models;
        $this->services = new \System\Services;
        $this->events = \System\Events::getInstance();
    }
}