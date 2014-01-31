<?php
namespace System;

abstract class Basemodel {
    protected $db;
    protected $models;
 
    function __construct() {
        $this->db = \System\Database::getInstance();
        $this->models = new \System\Models;
    }   
}