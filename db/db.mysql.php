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
     * @param string
     * @return PDOStatement
     **/
    public function query ($query = '')
    {
        $this->query = $query;
        if (empty($this->query)) {
            return false; 
        }
        $statement = $this->pdo->prepare($this->query);
        $statement->execute();
        return $statement;
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
     * @return integer
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
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }
    
    /**
     * @param string
     * @return array records
     **/
    public function fetchRecord ($query = '')
    {   
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetch();
    }
    
    /**
     * @param string
     * @return string id of inserted record
     **/
    public function insertRecord ($query)
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        if ($this->pdo->lastInsertId) {
            return $this->pdo->lastInsertId;
        }
        return false;
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
        $cols = empty($cols) ? '*' : $cols;
        if (is_array($params)) {
            $query = "SELECT $cols FROM $table WHERE " . implode(' AND ', $params);
            $statement = $this->pdo->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $statement->execute($params);
            return ($type === 'array') ? $statement->fetchAll() : $statement->fetch();
        }
        return false;
    }
    
    /**
     * @param string $table
     * @param array $array
     * @return mixed
     **/
    public function insertArray ($table, $array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $fields[] = $key;
                $values[] = $this->escape($value); 
            }
            $query = "INSERT INTO $table (" 
                . implode(', ', $fields) . ") VALUES (" 
                . implode(', ', $values) . ")";
            return $this->insertRecord($query);
        }
        return false;
    }
    
    /**
     * @param string $table
     * @param array $array
     * @param string $id
     * @return bool
     **/
    public function updateArray ($table, $array, $id)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $updates[] = "$key = " . $this->escape($value) . " ";
            }
            $query = "UPDATE $table SET " 
                . implode(', ', $updates) 
                . " WHERE $id";
            return $this->updateRecord($query);
        }
        return false;
    }
    
    /**
     * @param string $table
     * @param string $id
     * @return bool
     **/
    public function deleteArray ($table, $id)
    {
        $query = "DELETE FROM $table WHERE $id";
        return $this->deleteRecord($query);
    }
    
    /**
     * @param string $str
     * @return string
     * @todo review
     **/
    public function escape ($str)
    {   
        switch (gettype($str)) {
            case 'string':
                $str = "'" . $this->escape_str($str) . "'";
                break;
            case 'boolean':
                $str = ($str === FALSE) ? 0 : 1;
                break;
            default:
                $str = (($str == NULL) || ($str == ''))  ? "''" : "'" . $this->escape_str($str) . "'";
                break;
        }       
        return $str;
    }
    
    /**
     * @param string $str
     * @return string
     **/
    public function escape_str ($str)   
    {   
        if (get_magic_quotes_gpc()) {
            return $str;
        }
        if (function_exists('mysql_real_escape_string')) {
            return mysql_real_escape_string($str, $this->link);
        } elseif (function_exists('mysql_escape_string')) {
            return mysql_escape_string($str);
        } else {
            return addslashes($str);
        }
    }
    
    /**
     * @param string $query
     * @return bool
     **/
    public function deleteRecord ($query)
    {
        if ($rs = $this->query($query)) {
            return true;
        }
        return false;
    }
    
    /**
     * @param string $query
     * @return bool
     **/
    public function updateRecord ($query = '')
    {
        if ($rs = $this->query($query)) {
            return true;
        }
        return false;
    }
    
}
