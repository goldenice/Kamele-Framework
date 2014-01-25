<?php
class Sys_Basecontroller {
	protected $models;
	protected $services;
	
	function __construct() {
		$this->models = new Sys_Models;
		$this->services = new Sys_Services;
	}
}