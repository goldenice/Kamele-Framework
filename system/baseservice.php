<?php
abstract class Sys_Baseservice {
	protected $db;
    protected $models;
    protected $services;
    
    function __construct() {
    	$this->db = Sys_Database::getInstance();
    	$this->models = new Sys_Models;
        $this->services = new Sys_Services;
    }
}