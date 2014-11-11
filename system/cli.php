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
	static protected $commands = array(
		'help' => array(
		        'class'    => '\System\Cli',
				'function' => 'commandHelp',
				'helptext' => 'The help-command is meant to help the user on their way. How meta.'
			),
		'quit' => array(
		        'class'    => '\System\Cli',
				'function' => 'commandQuit',
				'helptext' => 'Quitting Kamele Interactive Shell, easy and simple.'
			)
		);
	
	/**
	 * Interactive runtime
	 * 
	 * @return  void
	 */
	public function interactive() {
	    $this->output('Kamele Interactive Shell Started');
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
		if (!isset(self::$commands[$parts[0]])) {
			$this->output('Unknown command');
			return false;
		}
		else {
		    $func = self::$commands[$parts[0]]['class'].'::'.self::$commands[$parts[0]]['function'];
			$this->output(call_user_func_array($func, array(&$parts)));
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
		fputs(STDOUT, $output."\n".' Kamele > ');
	}
	
	/**
	 * Help function, shows simple help text
	 * 
	 * @param	Array		$args		The arguments, with argument0 being the command called
	 * @return	string
	 */
	static function commandHelp($args) {
		$output = 'Kamele has the following commands built-in:'."\n";
		foreach (self::$commands as $command => $data) {
			$output .= "\n".$command."\t\t".$data['helptext'];
		}
		return $output;
	}
	
	/**
	 * Quitting function
	 * 
	 * @param	Array		$args		The arguments
	 * @return	string
	 */
	 static function commandQuit() {
	 	exit('Quitting'."\n");
	 }

}
