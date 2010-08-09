<?php if (!defined('SITE')) exit('No direct script access allowed');


/**
* Validation class
* 
* @version 1.0
* @author Vaska 
*/
class Processor 
{   
    var $error = array();
    var $variable = "";
    var $descriptor = "";
    var $tests = "";
    var $flag = false;
    var $required;
    var $rs;
    var $extra = false;

    
    /**
    * Returns validated string
    *
    * @param string $descrip
    * @param array $test
    * @param string $extra
    * @return string
    */
    function process($descrip, $tests, $extra='')
    { 
        $this->descriptor = $descrip;
    
        if (isset($extra)) $this->extra = $extra;
        
        if (!isset($_POST[$this->descriptor])) 
        {
            $this->variable = NULL;
        } 
        else 
        {
            $this->variable = $_POST[$this->descriptor];
        }
        
        foreach ($tests as $test) 
        {
            $this->variable = $this->$test();
        }
            
        return $this->variable;
    }
    
    
    /**
    * Returns boolean
    *
    * @param void
    * @return boolean
    */
    function check_errors()
    {
        return $this->flag;
    } 

    /**
    * Returns array of errors
    *
    * @param void
    * @return array
    */
    function get_errors()
    {
        return $this->error;
    }

    /**
    * Return force error boolean
    *
    * @param void
    * @return boolean
    */
    // a way to make sure we return errors
    function force_error()
    {
        $this->error['error'] = TRUE;
    }


    /**
    * Return string
    * (checks string lenght)
    *
    * @param void
    * @return string
    */
    function reqNotEmpty()
    {
        if (strlen($this->variable) == "0") 
        {
            $this->error[$this->descriptor] = " (Required)";
            $this->flag[$this->descriptor] = true;
            return '';
        } 
        else 
        {
            return $this->variable; 
        }
    }
    

    /**
    * Return string
    * (using strip tags function)
    *
    * @param void
    * @return string
    */
    function notags()
    {
        if ($this->variable) 
        {
            $out = strip_tags($this->variable);
            return $out;
        } 
        else 
        {
            return NULL;
        }
    }
    
    
    /**
    * Returns string
    * (strips php tags)
    *
    * @param void
    * @return string
    */
    function nophp()
    {
        if ($this->variable) 
        {
            $out = preg_replace('|<\?[php]?(.)*\?>|sUi', '', $this->variable);
            return $out;
        } 
        else 
        {
            return NULL;
        }
    }
    
    
    /**
    * Returns boolean
    * (force boolean)
    *
    * @param void
    * @return boolean
    */
    function boolean()
    {
        if ($this->variable == 1) 
        {
            return 1;
        } 
        else 
        {
            return 0;
        }
    }
    
    
    /**
    * Return string
    * (letters only, no spaces)
    *
    * @param void
    * @return string
    */
    function alpha()
    {
        if ($this->variable) 
        {
            $out = preg_replace('/[^a-z0-9-]/i', '', $this->variable);
            return $out;
        } 
        else 
        {
            return NULL;
        }
    }
    
    
    /**
    * Returns string
    * (numbers only, no spaces)
    *
    * @param void
    * @return string
    */
    function digit()
    {
        if ($this->variable) 
        {
            return ((int) $this->variable) ? (int) $this->variable : NULL;
        } 
        else 
        {
            return NULL;
        }
    }
    
    
    /**
    * Returns string
    * (this should not be used for titles as it allows characters)
    *
    * @param void
    * @return string
    */
    function alphanum()
    {
        if ($this->variable) 
        {
            $out = preg_replace('/[^[:alnum:]|[:blank:]]/', '', $this->variable);
            return $out;
        } 
        else 
        {
            return NULL;
        }
    }
    
    
    /**
    * Returns string
    * (specific to login/passwords)
    *
    * @param void
    * @return string
    */
    function length12()
    {
        if ((strlen($this->variable) > 12) && (strlen($this->variable) < 6))
        {
            $this->error[$this->descriptor] = " (Too many characters)";
            $this->flag[$this->descriptor] = true;
            return '';
        } 
        else 
        {
            return $this->variable; 
        }
    }
    
    
    /**
    * Return false on error
    * (letters/numbers only, no spaces)
    *
    * @param void
    * @return mixed
    */
    function pchars()
    {
        // FIX LATER
        $OBJ =& get_instance();
        
        if ($this->variable) 
        {
            if (preg_match('/^[a-zA-Z0-9]+$/', $this->variable)) 
            {
                return $this->variable;
            }
            else
            {
                $this->force_error();
                // FIX LATER
                $OBJ->template->action_error = 'invalid input';
                
                $this->error[$this->descriptor] = " (Invalid Input)";
                $this->flag[$this->descriptor] = true;
                return NULL;
            }
        } 
        else 
        {
            return NULL;
        }
    }
}
