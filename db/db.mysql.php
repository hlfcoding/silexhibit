<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Database Class
* 
* @version 1.0
* @author Vaska
*/
    
class Db
{
    var $theQuery;
    var $link;

    
    /**
    * Construct 
    *
    * @param void
    * @return mixed
    */
    function Db()
    {
        $this->initialize();
        $this->setNames();
    }


    /**
    * Return database connection
    *
    * @param void
    * @return mixed
    */
    function initialize()
    {
        global $indx;
        
        if (!$indx['host']) $this->db_out_of_order();
    
        $this->link = @mysql_connect($indx['host'], $indx['user'], $indx['pass']);
            
        if (!$this->link) $this->db_out_of_order();

        mysql_select_db($indx['db']);
        register_shutdown_function(array(&$this, 'close'));
    }


    /**
    * Returns query
    *
    * @param string $query
    * @return mixed
    */
    function query($query='')
    {
        $this->theQuery = $query;
        if (!$this->theQuery) return false; 
        return mysql_query($this->theQuery, $this->link);
    }
        

    /**
    * Sets the database to be utf-8 
    *
    * @param void
    * @return null
    */
    function setNames()
    {
        $this->query("SET NAMES 'utf8'");
        return;
    }


    /**
    * Returns count
    * (are we even using this?)
    *
    * @param string $query
    * @return integer
    */
    function getCount($query='')
    {
        if ($rs = $this->query($query)) 
        {
            $num = (mysql_num_rows($rs) != 0) ? mysql_result($rs,0) : '';
            mysql_free_result($rs);
            return $num;
        }
        
        return 0;
    }


    /**
    * Returns array of records
    *
    * @param string $query
    * @return mixed
    */
    function fetchArray($query='')
    {
        $rs = $this->query($query);
        
        if ($rs) {
            if (mysql_num_rows($rs) > 0) 
            {
                while ($arr = mysql_fetch_assoc($rs)) $out[] = $arr;
                return $out;
            }
        }
        
        return false;
    }

    
    /**
    * Returns array of record
    *
    * @param string
    * @return mixed
    */
    function fetchRecord($query='')
    {   
        $rs = $this->query($query);
        
        if ($rs) {
            if (mysql_num_rows($rs) > 0) 
            {
                $arr = mysql_fetch_assoc($rs);          
                return $arr;
            }
        }
        
        return false;
    }


    /**
    * Returns id of inserted record
    *
    * @param string $query
    * @return mixed
    */
    function insertRecord($query)
    {
        if ($rs = $this->query($query))
        {
            $lastid = mysql_insert_id($this->link);
            if ($lastid) return $lastid;
        }
        
        return false;
    }
    
    
    /**
    * Returns array of record(s)
    *
    * @param string $table
    * @param array $array
    * @param string $type
    * @param string $cols
    * @return mixed
    */
    function selectArray($table, $array, $type='array', $cols='')
    {
        $cols = ($cols == '') ? '*' : $cols;
        
        if (is_array($array))
        {
            foreach ($array as $key => $value)
            {
                $select[] = "$key = " . $this->escape($value) . " ";
            }

            $query = "SELECT $cols FROM $table WHERE 
                " . implode(' AND ', $select) . "";
                
            if ($type == 'array')
            {
                return $this->fetchArray($query);
            }
            else
            {
                return $this->fetchRecord($query);
            }
        }

        return false;
    }
    

    /**
    * Returns id of inserted record
    *
    * @param string $table
    * @param array $array
    * @return mixed
    */
    function insertArray($table, $array)
    {
        if (is_array($array))
        {
            foreach ($array as $key => $value)
            {
                $fields[] = $key;
                $values[] = $this->escape($value); 
            }
            
            $query = "INSERT INTO $table 
                (" . implode(', ', $fields) . ") 
                VALUES 
                (" . implode(', ', $values) . ")";

            return $this->insertRecord($query);
        }

        return false;
    }

    
    /**
    * Returns boolean
    *
    * @param string $table
    * @param array $array
    * @param string $id
    * @return bool
    */
    function updateArray($table, $array, $id)
    {
        if (is_array($array))
        {
            foreach ($array as $key => $value)
            {
                $updates[] = "$key = " . $this->escape($value) . " ";
            }
            
            $query = "UPDATE $table SET 
                " . implode(', ', $updates) . " 
                WHERE $id";

            return $this->updateRecord($query);
        }

        return false;
    }
    
    
    /**
    * Returns boolean
    *
    * @param string $table
    * @param string $id
    * @return bool
    */
    function deleteArray($table, $id)
    {
        $query = "DELETE FROM $table WHERE $id";
        return $this->deleteRecord($query);
    }
    
    
    /**
    * Returns string
    *
    * @param string $str
    * @return string
    */
    function escape($str)
    {   
        switch (gettype($str))
        {
            case 'string'   :   $str = "'".$this->escape_str($str)."'";
                break;
            case 'boolean'  :   $str = ($str === FALSE) ? 0 : 1;
                break;
            
            //review
            default         :   $str = (($str == NULL) || ($str == ''))  ? "''" : "'".$this->escape_str($str)."'";
                break;
        }       

        return $str;
    }
    
    
    /**
    * Returns string
    *
    * @param string $str
    * @return string
    */
    function escape_str($str)   
    {   
        if (get_magic_quotes_gpc())
        {
            return $str;
        }

        if (function_exists('mysql_real_escape_string'))
        {
            return mysql_real_escape_string($str, $this->link);
        }
        elseif (function_exists('mysql_escape_string'))
        {
            return mysql_escape_string($str);
        }
        else
        {
            return addslashes($str);
        }
    }
    

    /**
    * Returns boolean
    *
    * @param string $query
    * @return bool
    */
    function deleteRecord($query)
    {
        if ($rs = $this->query($query))
        {
            return true;
        }
        
        return false;
    }


    /**
    * Returns boolean
    *
    * @param string $query
    * @return bool
    */
    function updateRecord($query='')
    {
        if ($rs = $this->query($query)) 
        {
            return true;
        }
        
        return false;
    }


    /**
    * Returns object - closes conenction 
    *
    * @param void
    * @return objet
    */
    function close()
    {
        mysql_close($this->link);
    }


    /**
    * Returns error
    *
    * @param void
    * @return string
    */
    function db_out_of_order() 
    {
        show_error('Database is unavailable');
        exit;
    }
}
