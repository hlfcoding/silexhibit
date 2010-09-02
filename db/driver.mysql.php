<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * MySQL Database Class
 * Very basic wrapper for PDO
 * @version 1.1
 * @package Indexhibit
 * @author Vaska
 * @author Peng Wang <peng@pengxwang.com>
 * @todo loosen tight coupling to indexhibit
 **/

class MySQLDriver 
{
    public $query;
    public $link;
    public $pdo;
    
    protected $tables;
    protected $info;
    
    const FETCH_ARRAY = 0;
    const FETCH_RECORD = 1;

    public function __construct ($info = null, $tables = null)
    {
        if (is_null($info)) {
            global $indx;
            if (MODE === DEVELOPMENT) {
                $info = array(
                    'host' => $indx['dev_host'],
                    'user' => $indx['dev_user'],
                    'pass' => $indx['dev_pass'],
                    'db' => $indx['db']
                );
            } else if (MODE === PRODUCTION) {
                $info = array(
                    'host' => $indx['host'],
                    'user' => $indx['user'],
                    'pass' => $indx['pass'],
                    'db' => $indx['db']
                );            
            }
        }
        $this->info = $info;
        if (is_null($tables)) {
            global $tables;
        }
        $this->tables = $tables;
        $this->initialize();
        $this->setNames();
        if (MODE === DEVELOPMENT) {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * TODO description
     * @global array config
     **/
    protected function initialize ()
    {
        if (!isset($this->info['host']) || empty($this->info['host'])) {
            show_error('Database is unavailable');
        }
        try {
            $this->pdo = new PDO("mysql:host={$this->info['host']};dbname={$this->info['db']}", 
                $this->info['user'], $this->info['pass']);
        } catch (PDOException $e) {
            show_error('Database is unavailable');
            die ();
        }
    }
    
    /**
     * Tailored workhorse for all queries
     * Also saves the query
     * @param string
     * @param array
     * @param array
     * @return int affected rows for INSERT or UPDATE or DELETE statements
     * @return PDOStatement|false result for SELECT statements
     **/
    protected function query ($query = '', $params = array(), $driver_options = array())
    {
        // _log($query);
        $this->query = trim($query);
        if (empty($params)) {
            return (strpos($this->query, 'SELECT') === 0) 
                ? $this->pdo->query($this->query) // result set
                : $this->pdo->exec($this->query); // affected rows
        } else { // parameter binding
            $statement = $this->pdo->prepare($this->query, $driver_options);
            $statement->execute($this->queryParams($params));
            if (strpos($this->query, 'SELECT') === 0) {
                $statement->setFetchMode(PDO::FETCH_ASSOC);
                return $statement; // result set
            } else { 
                return $statement->rowCount(); // affected rows 
            }
        }
    }
    
    /**
     * Makes query template for parameter binding
     * @param array
     * @return array
     **/
    protected function querySegments ($params) 
    {
        $querySegments = array();
        foreach (array_keys($params) as $field) {
            $querySegments[] = "$field = :$field";
        }
        return $querySegments;
    }
    
    /**
     * Updates the keys for parameter binding
     * @param array
     * @return array
     **/
    protected function queryParams ($params) 
    {
        $queryParams = array();
        foreach ($params as $field => $value) {
            $queryParams[":$field"] = $value;
        }
        return $queryParams;
    }
    
    /**
     * Sets the database to be utf-8 
     * @todo this seems basic
     **/
    protected function setNames () 
    {
        $this->query("SET NAMES 'utf8'");
    }
    
    /**
     * @param string key
     * @return string name
     * @todo check for prefix
     **/
    public function table ($key) 
    {
        if (array_key_exists($key, $this->tables)) {
            return $this->tables[$key];
        } else {
            throw new PDOException("table name at `$key` does not exist");
            return false;
        }
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
    public function fetchArray ($query = '', $params = array())
    {
        return $this->query($query, $params)
            ->fetchAll();
    }
    
    /**
     * @param string
     * @return array records
     **/
    public function fetchRecord ($query = '', $params = array())
    {   
        return $this->query($query, $params)
            ->fetch();
    }
        
    /**
     * @param string
     * @param array
     * @param string
     * @param string
     * @return array
     * @todo default $type as constant
     **/
    public function selectArray ($table, $params, $type = null, $cols = '', $end = '')
    {
        if (!is_array($params)) {
            throw new PDOException('no conditions to match');
            return false;
        }
        $tables = is_array($table) ? $table : array($table);
        $a = array();
        foreach ($tables as $t) {
            $a[] = $this->table($t);
        }
        $table = implode(', ', $a);
        $type = is_null($type) ? self::FETCH_ARRAY : $type;
        $cols = is_array($cols) ? implode(', ', $cols) : $cols;
        $cols = empty($cols) ? '*' : $cols;
        $query = "SELECT $cols FROM $table ";
        $query .= empty($params) ? '' : "WHERE " . implode(' AND ', $this->querySegments($params)); 
        $query .= " $end";
        return ($type === self::FETCH_ARRAY) 
            ? $this->fetchArray($query, $params) 
            : $this->fetchRecord($query, $params);
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
        $table = $this->table($table);
        $query = "INSERT INTO $table (" . implode(', ', array_keys($params)) 
            . ") VALUES (" . implode(', ', array_values($params)) . ")";
        return $this->query($query, $params);
    }
    
    /**
     * @param string $table
     * @param array $array
     * @param string $id
     * @return bool
     * @todo prepare where clause
     **/
    public function updateArray ($table, $params, $id = null)
    {
        if (!is_array($params)) {
            throw new PDOException('nothing to update to');
            return false;
        }
        $table = $this->table($table);
        $id = addslashes($id);
        $query = "UPDATE $table SET " . implode(', ', $this->querySegments($params)) 
            . (is_null($id) ? '' : " WHERE $id");
        return $this->query($query, $params) > 0;
    }
    
    /**
     * @param string $table
     * @param string $id
     * @return bool
     * @todo prepare where clause
     **/
    public function deleteArray ($table, $id)
    {
        $table = $this->table($table);
        $id = addslashes($id);
        $query = "DELETE FROM $table WHERE $id";
        return $this->query($query) > 0;
    }
}