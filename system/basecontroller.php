<?php
namespace System;
if (!defined('SYSTEM')) exit('No direct script access allowed');

abstract class Basecontroller {
	protected $loader;
    protected $events;
	
	function __construct() {
		$this->loader = \System\Loader::getInstance();
        $this->events = \System\Events::getInstance();
	}
}