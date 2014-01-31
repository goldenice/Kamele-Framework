<?php
namespace System;

class Database extends Singleton {
    private $handler;
    
    function __construct($host = DB_HOST, $user = DB_USER, $pass = DB_PASS, $name = DB_NAME) {
        $this->handler = mysqli_connect($host, $user, $pass, $name);
        if ($this->handler == null or $this->handler == false) {
            return false;
            $this->handler = null;
        }
        else {
            return true;
        }
    }
    
    function query($query) {
        return $this->handler->query($query);
    }
    
    function escape($input) {
        return $this->handler->real_escape_string($input);
    }
    
    function fetchAssoc($input) {
        return mysqli_fetch_assoc($input);
    }
    
    function numRows($input) {
		return mysqli_num_rows($input);
	}
    
    function toArray($input) {
        $output = array();
        while ($a = $this->fetchAssoc($input)) {
            $output[] = $a;
        }
        return $output;
    }
	
	function mysqlError() {
		return mysqli_error($this->handler);
	}
    
    function safeQuery($query, $input = array()) {
        $repl = array();
        $vals = array();
        foreach ($input as $k=>$v) {
            $repl[] = '{{'.$k.'}}';
            $vals[] = $v;
        }
        $query = str_replace($repl, $vals, $query);
        return $this->query($query);
    }
}