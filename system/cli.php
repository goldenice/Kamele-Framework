<?php
namespace System;
if (!defined('SYSTEM')) exit('No direct script access allowed');

/**
 * CLI class
 * Implements interactive mode and command line access to the framework
 * 
 * @package     Kamele Framework
 * @subpackage  System
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.5
 */
class Cli extends Singleton {
	
	/**
	 * List of available commands mapped to functions
	 * @var		Array
	 */
	protected $commands = array(
		'help' => array(
				'function' => '\System\Cli::help',
				'helptext' => 'The help-command is meant to help the user on their way. How meta.'
			)
		);
	
	/**
	 * Interactive runtime
	 * 
	 * @return  void
	 */
	public function interactive() {
		while ($line = trim(fgets(STDIN))) {
			if ($line == '') continue;
			$this->interpret($line);
		}
	}
	
	/**
	 * Interpreter
	 * 
	 * @param 	string		$command		Command according to commandline
	 * @return 	boolean
	 */
	protected function interpret($command) {
		$parts = explode(' ', $command);
		if (!isset($commands[$parts[0]])) {
			$this->output('Unknown command');
			return false;
		}
		else {
			$this->output($commands[$parts[0]]['function']($parts));
			return true;
		}
	}
	
	/**
	 * Output
	 * 
	 * @param	string		$output			The text to output
	 * @return	void
	 */
	protected function output($output) {
		fputs(STDOUT, $output);
	}
	
	/**
	 * Help function, shows simple help text
	 * 
	 * @param	Array		$args		The arguments, with argument0 being the command called
	 * @return	string
	 */
	protected static function help($args) {
		return 'HELP WAS CALLED!!!1!';
	}
	
}
