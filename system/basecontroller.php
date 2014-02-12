<?php
namespace System;

abstract class Basecontroller {
	protected $loader;
    protected $events;
	
	function __construct() {
		$this->loader = \System\Loader::getInstance();
        $this->events = \System\Events::getInstance();
	}
}