<?php
namespace System;

abstract class Basecontroller {
	protected $models;
	protected $services;
	
	function __construct() {
		$this->models = new \System\Models;
		$this->services = new \System\Services;
	}
}