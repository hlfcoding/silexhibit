<?php if (!defined('SITE')) exit('No direct script access allowed');
/**
 * Database Class
 * @version 1.1
 * @package Indexhibit
 * @author Vaska
 * @author Peng Wang <peng@pengxwang.com>
 * @todo use PDO
 **/
class Db
{
    public $theQuery;
    public $link;

    public function __construct ()
    {
        $this->initialize();
        $this->setNames();
    }

    public function initialize ()
    {
        global $indx;
        if (!isset($indx['host']) || empty($indx['host'])) {
            $this->db_out_of_order();
        }
        $this->link = @mysql_connect($indx['host'], $indx['user'], $indx['pass']);
        if (!isset($this->link) || empty($this->link)) { 
            $this->db_out_of_order();
        }
        mysql_select_db($indx['db']);
        register_shutdown_function(array(&$this, 'close'));
    }
    
    /**
     * @param string $query
     * @return mixed
     **/
    public function query ($query = '')
    {
        $this->theQuery = $query;
        if (empty($this->theQuery)) {
            return false; 
        }
        return mysql_query($this->theQuery, $this->link);
    }
    
    /**
     * Sets the database to be utf-8 
     **/
    public function setNames ()
    {
        $this->query("SET NAMES 'utf8'");
        return;
    }
    
    /**
     * @param string $query
     * @return integer
     **/
    public function getCount ($query = '')
    {
        if ($rs = $this->query($query)) {
            $num = (mysql_num_rows($rs) != 0) ? mysql_result($rs,0) : '';
            mysql_free_result($rs);
            return $num;
        }
        return 0;
    }
    
    /**
     * @param string $query
     * @return array records
     **/
    public function fetchArray ($query = '')
    {
        $rs = $this->query($query);
        if ($rs) {
            if (mysql_num_rows($rs) > 0) {
                while ($arr = mysql_fetch_assoc($rs)) $out[] = $arr;
                return $out;
            }
        }
        return false;
    }
    
    /**
     * @param string
     * @return array records
     **/
    public function fetchRecord ($query = '')
    {   
        $rs = $this->query($query);
        if ($rs) {
            if (mysql_num_rows($rs) > 0) {
                $arr = mysql_fetch_assoc($rs);          
                return $arr;
            }
        }
        return false;
    }
    
    /**
     * @param string $query
     * @return int id of inserted record
     **/
    public function insertRecord ($query)
    {
        if ($rs = $this->query($query)) {
            $lastid = mysql_insert_id($this->link);
            if ($lastid) {
                return $lastid;
            }
        }
        return false;
    }
    
    /**
     * @param string $table
     * @param array $array
     * @param string $type
     * @param string $cols
     * @return array records
     **/
    public function selectArray ($table, $array, $type = 'array', $cols = '')
    {
        $cols = ($cols == '') ? '*' : $cols;
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $select[] = "$key = " . $this->escape($value) . " ";
            }
            $query = "SELECT $cols FROM $table WHERE " 
                . implode(' AND ', $select) . "";
            if ($type === 'array') {
                return $this->fetchArray($query);
            } else {
                return $this->fetchRecord($query);
            }
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
    
    public function close ()
    {
        mysql_close($this->link);
    }
    
    public function db_out_of_order () 
    {
        show_error('Database is unavailable');
        exit;
    }
}
