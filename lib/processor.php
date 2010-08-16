<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Validation class
 * @version 1.1
 * @package Indexhibit
 * @author Vaska 
 * @author Peng Wang <peng@pengxwang.com>
 **/
class Processor 
{   
    public $error = array();
    public $variable = "";
    public $descriptor = "";
    public $tests = "";
    public $flag = false;
    public $required;
    public $rs;
    public $extra = false;
    
    /**
     * @param string
     * @param array
     * @param string
     * @return string validated
     **/
    public function process ($descrip, $tests, $extra = '')
    { 
        $this->descriptor = $descrip;
        if (isset($extra)) {
            $this->extra = $extra;
        }
        if (!isset($_POST[$this->descriptor])) {
            $this->variable = null;
        } else {
            $this->variable = $_POST[$this->descriptor];
        }
        foreach ($tests as $test) {
            $this->variable = $this->$test();
        }
        return $this->variable;
    }
    
    /**
     * @return boolean
     **/
    public function check_errors ()
    {
        return $this->flag;
    } 

    /**
     * @return array
     **/
    public function get_errors ()
    {
        return $this->error;
    }

    /**
     * To make sure we return errors
     * @param void
     * @return boolean force errors
     **/
    public function force_error ()
    {
        $this->error['error'] = true;
    }

    /**
     * Checks string length
     * @return string
     **/
    public function reqNotEmpty ()
    {
        if (strlen($this->variable) === 0) 
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
     * @uses strip_tags()
     * @return string|null
     * @todo deprecate, just use strip_tags
     **/
    public function notags ()
    {
        if ($this->variable) {
            $out = strip_tags($this->variable);
            return $out;
        } else  {
            return null;
        }
    }
    
    /**
     * strip php tags
     * @return string
     **/
    public function nophp ()
    {
        if ($this->variable) {
            $out = preg_replace('|<\?[php]?(.)*\?>|sUi', '', $this->variable);
            return $out;
        } else {
            return null;
        }
    }
    
    /**
     * Get force boolean
     * @return boolean
     **/
    public function boolean ()
    {
        if ($this->variable == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    
    /**
     * Letters only, no spaces
     * @return string
     **/
    public function alpha ()
    {
        if ($this->variable) {
            $out = preg_replace('/[^a-z0-9-]/i', '', $this->variable);
            return $out;
        } else {
            return null;
        }
    }
    
    /**
     * Returns string
     * (numbers only, no spaces)
     *
     * @param void
     * @return string
     **/
    public function digit ()
    {
        if ($this->variable) {
            return ((int) $this->variable) ? (int) $this->variable : null;
        } else {
            return null;
        }
    }
    
    /**
     * This should not be used for titles as it allows characters
     * @return string
     **/
    public function alphanum ()
    {
        if ($this->variable) {
            $out = preg_replace('/[^[:alnum:]|[:blank:]]/', '', $this->variable);
            return $out;
        } else {
            return null;
        }
    }
    
    /**
     * Specific to login/passwords
     * @return string
     * @todo rename, abstract
     **/
    function length12()
    {
        if ((strlen($this->variable) > 12) && (strlen($this->variable) < 6)) {
            $this->error[$this->descriptor] = " (Too many characters)";
            $this->flag[$this->descriptor] = true;
            return '';
        } else {
            return $this->variable; 
        }
    }
    
    /**
     * Letters/numbers only, no spaces
     * @param void
     * @return mixed
     * @todo fix later
     **/
    public function pchars ()
    {
        $OBJ =& get_instance();
        if ($this->variable) {
            if (preg_match('/^[a-zA-Z0-9]+$/', $this->variable)) {
                return $this->variable;
            } else {
                $this->force_error();
                $OBJ->template->action_error = 'invalid input';
                $this->error[$this->descriptor] = " (Invalid Input)";
                $this->flag[$this->descriptor] = true;
                return null;
            }
        } else {
            return null;
        }
    }
}
