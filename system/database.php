<?php
namespace System;
use PDO;

class Database extends Singleton {
    private $handler;
    private $dbtype = 'mysql';
    
    function __construct($host = DB_HOST, $user = DB_USER, $pass = DB_PASS, $name = DB_NAME) {
        $this->handler = new PDO($this->dbtype.':dbname='.$name.';host='.$host, $user, $pass);
        $this->handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
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
    
    function fetchAssoc($input) {
        return $input->fetch(PDO::FETCH_ASSOC);
    }
    
    function numRows($input) {
		return $input->rowCount();
	}
    
    function toArray($input) {
        return $input->fetchAll();
    }
	
	function mysqlError($input = null) {
		if ($input != null) return $input->errorInfo();
        else return $this->handler->errorInfo();
	}
    
    function safeQuery($query, $input = array()) {
        $prep = $this->handler->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        foreach ($input as $k=>$v) {
            $input[':'.$k] = $v;
            unset($input[$k]);
        }
        $prep->execute($input);
        return $prep;
    }
}