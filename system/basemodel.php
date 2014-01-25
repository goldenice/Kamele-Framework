<?php
abstract class Sys_Basemodel {
    protected $db;
    protected $models;
 
    function __construct() {
        $this->db = Sys_Database::getInstance();
        $this->models = new Sys_Models;
    }   
}