<?php
namespace System;
use PDO;
if (!defined('SYSTEM')) exit('No direct script access allowed');

/**
 * A basic databaseconnection handler, based on PDO
 * 
 * @package     Kamele Framework
 * @subpackage  System
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.0-beta2
 */
class Database extends Singleton {
    /**
     * @access  private
     * @var     PDO-instance    An instance of the PDO class, which is connected to the database
     */
    private $handler;
    
    /**
     * @access  private
     * @var     string          Defines the databasetype for PDO. Perhaps someday this will be an option
     */
    private $dbtype;
    
    /**
     * @access	public
     * @var		int				Counts the number of queries executed
     */
    public $querycount = 0;
    
    /**
     * Constructor function, accepts custom arguments for DB connect-info
     * 
     * @access  public
     * @param   string      $host       The database host
     * @param   string      $user       Database-username
     * @param   string      $pass       Plaintext database password
     * @param   string      $name       The name of the database to connect to
     * @return  boolean
     */
    public function __construct($host = DB_HOST, $user = DB_USER, $pass = DB_PASS, $name = DB_NAME, $type = DB_TYPE) {
        $this->dbtype = $type;
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
    
    /**
     * Executes query in a way that is not safe for input
     * Escaping has to be done manually when using this function
     * Because of that, this function is deprecated
     * 
     * @access  public 
     * @deprecated
     * @param   string      $query      The query to execute in the database
     * @return  PDOStatement-object
     */
    public function query($query) {
    	$this->querycount++;
        return $this->handler->query($query);
    }
    
    /**
     * Fetches a single row in an associative array from a query-result-object
     * 
     * @access  public
     * @param   PDOStatement    $input      The result object to get the row from
     * @return  array
     */
    public function fetchAssoc($input) {
        return $input->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Returns the number of affected rows from a PDOStatement-object
     * 
     * @access  public
     * @param   PDOStatement    $input      The result object
     * @return  int
     */
    public function numRows($input) {
		return $input->rowCount();
	}
    
    /**
     * Returns an multi-dimensional associative array with all the rows from the result object
     * 
     * @access  public
     * @param   PDOStatement    $input      The result object
     * @return  array
     */
    public function toArray($input) {
        return $input->fetchAll();
    }
	
    /**
     * Returns the latest database-error
     * WARNING: Deprecated because name is mysql-specific, and this is a general purpose databaseclass
     * 
     * @access  public
     * @param   ?               $input      Get error from another PDO object
     * @return  string
     * @deprecated
     */
	public function mysqlError($input = null) {
		if ($input != null) {
            return $input->errorInfo();
		}
        return $this->handler->errorInfo();
	}
    
    /**
     * Returns the latest database-error
     * 
     * @access  public
     * @param   ?               $input      Get error from another PDO object
     * @return  string
     */
    public function lastError($input = null) {
		if ($input != null) {
            return $input->errorInfo();
		}
        return $this->handler->errorInfo();
	}
    
    /**
     * Executes query, replaces placeholders in that query with PDO
     * Is used to execute queries with automatically sanitized inputs
     * 
     * @access  public
     * @param   string      $query      The query to execute, with placeholders in form ':placeholder'
     * @param   array       $input      The placeholders to replace, in form key=>value, for example 'placeholder'=>'data'
     * @return  PDOStatement
     */
    function safeQuery($query, $input = array()) {
    	$this->querycount++;
        $prep = $this->handler->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        foreach ($input as $k=>$v) {
            $input[':'.$k] = $v;
            unset($input[$k]);
        }
        $prep->execute($input);
        return $prep;
    }
}