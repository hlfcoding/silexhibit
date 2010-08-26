<?php if (!defined('SITE')) exit('No direct script access allowed');
/**
 * Database Class
 * Very basic wrapper for PDO
 * @version 1.1
 * @package Indexhibit
 * @author Vaska
 * @author Peng Wang <peng@pengxwang.com>
 * @todo use PDO
 **/
class Db
{
    public $query;
    public $link;
    public $pdo;

    public function __construct ()
    {
        $this->initialize();
        $this->setNames();
    }

    /**
     * TODO description
     * @global array config
     **/
    public function initialize ()
    {
        global $indx;
        if (!isset($indx['host']) || empty($indx['host'])) {
            show_error('Database is unavailable');
        }
        try {
            $this->pdo = new PDO("mysql:host={$indx['host']};dbname={$indx['db']}", $indx['user'], $indx['pass']);
        } catch (PDOException $e) {
            show_error('Database is unavailable');
            die ();
        }
    }
    
    /**
     * Also saves the query
     * @param string
     * @param array
     * @param array
     * @return int affected rows for INSERT or UPDATE or DELETE statements
     * @return PDOStatement|false result for SELECT statements
     **/
    protected function query ($query = '', $params = array(), $driver_options = array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY))
    {
        $this->query = trim($query);
        if (empty($params)) {
            return (strpos($this->query, 'SELECT') === 0) ? $this->pdo->query($query) : $this->pdo->exec($query);
        } else {
            $statement = $this->pdo->prepare($query, $driver_options);
            $statement->execute($params);
            return (strpos($this->query, 'SELECT') === 0) ? $statement : $statement->rowCount();
        }
    }
    
    /**
     * TODO description
     * @param array
     * @return array
     **/
    protected function querySegments ($params) {
        $querySegments = array();
        foreach (array_keys($params) as $field) {
            $querySegments[] = "$field = :$field";
        }
        return $querySegments;
    }
    
    /**
     * Sets the database to be utf-8 
     * @todo this seems basic
     **/
    public function setNames ()
    {
        $this->query("SET NAMES 'utf8'");
        return;
    }
    
    /**
     * @param string
     * @return int
     **/
    public function getCount ($query = '')
    {
        $statement = $this->query($query);
        return $statement ? $statement->rowCount() : 0;
    }
    
    /**
     * @param string
     * @return array records
     **/
    public function fetchArray ($query = '')
    {
        $statement = $this->query($query);
        return $statement->fetchAll();
    }
    
    /**
     * @param string
     * @return array records
     **/
    public function fetchRecord ($query = '')
    {   
        $statement = $this->query($query);
        return $statement->fetch();
    }
        
    /**
     * @param string
     * @param array
     * @param string
     * @param string
     * @return array
     * @todo default $type as constant
     **/
    public function selectArray ($table, $params, $type = 'array', $cols = '')
    {
        if (!is_array($params)) {
            throw new PDOException('no conditions to match');
            return false;
        }
        $cols = empty($cols) ? '*' : $cols;
        $query = "SELECT $cols FROM $table WHERE " . implode(' AND ', $this->querySegments($params));
        $statement = $this->query($query, $params);
        return ($type === 'array') ? $statement->fetchAll() : $statement->fetch();
    }
    
    /**
     * @param string
     * @param array
     * @return int|FALSE
     **/
    public function insertArray ($table, $params)
    {
        if (!is_array($params)) {
            throw new PDOException('nothing to insert');
            return false;
        }
        $query = "INSERT INTO $table (" . implode(', ', array_keys($params)) 
            . ") VALUES (" . implode(', ', array_values($params)) . ")";
        return $this->query($query, $params);
    }
    
    /**
     * @param string $table
     * @param array $array
     * @param string $id
     * @return bool
     **/
    public function updateArray ($table, $params, $id)
    {
        if (!is_array($params)) {
            throw new PDOException('nothing to update to');
            return false;
        }
        $query = "UPDATE $table SET " . implode(', ', $this->querySegments($params)) . " WHERE $id";
        return $this->query($query) > 0;
    }
    
    /**
     * @param string $table
     * @param string $id
     * @return bool
     **/
    public function deleteArray ($table, $id)
    {
        $query = "DELETE FROM $table WHERE $id";
        return $this->query($query) > 0;
    }
    
}
