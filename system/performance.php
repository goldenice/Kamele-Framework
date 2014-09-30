<?php
namespace System;
if (!defined('SYSTEM')) exit('No direct script access allowed');

/**
 * This class can be used for measuring internal performance of Kamele
 * 
 * @package		Kamele Framework
 * @subpackage	System
 * @author		Rick Lubbers <me@ricklubbers.nl>
 * @since 		1.4
 */
class Performance extends Singleton {
	
	/**
	 * @access	private
	 * @var		float		Time in ms the system has started, to be used as reference point for future measurements
	 */
	private $time_system_start = 0;
	
	/**
	 * @access	public
	 * @return	bool
	 */
	public function systemStart() {
		$this->time_system_start = microtime(true)*1000;
		return true;
	}
	
	/**
	 * @access	public
	 * @return	int
	 */
	public function getQueryCount() {
		$db = Database::getInstance();
		return $db->querycount;
	}
	
	/**
	 * @access 	public
	 * @return	float
	 */
	public function getCurrentTime() {
		return (microtime(true)*1000) - $this->time_system_start;
	}
	
}