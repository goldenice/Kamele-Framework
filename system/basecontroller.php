<?php
namespace System;

abstract class Basecontroller {
	protected $models;
	protected $services;
    protected $events;
	
	function __construct() {
		$this->models = new \System\Models;
		$this->services = new \System\Services;
        $this->events = new \System\Events;
	}
}