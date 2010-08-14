<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Core class
*
* Loading tools
* 
* @version 1.0
* @author Vaska 
*/
class Core
{
    public $is_loaded;
    
    public $db;
    public $lang;
    public $template;
    public $access;
    public $front;
    public $organize;
    
    const DIRECTIVE_DELIMITER = '.';
    
    public function __construct()
    {
        $this->load_class('db');
    }
    
    /**
    * Return language and core classes
    *
    * @param void
    * @return array
    **/
    public function auto_load ()
    {
        foreach (array('lang', 'lib.template', 'lib.access') as $val) {
            $this->load_class($class);
        }
    }
    
    /**
     * Library loader
     * @param string Class name
     * @return class object
     **/
    public function lib_class ($class)
    {
        if (empty($class)) {
            throw new RuntimeException('class to load not specified');
        }
        $class = "lib.$class";
        return $this->load_class($class);
    }
    
    /**
     * Base loader method, uses a directive, unlike the global loader function
     * @param string Class directive as `[class].[type]`
     * @return class object
     **/
    protected function load_class ($class) 
    {
        $class = strtolower($class);
        $type = null;
        $_pieces = explode(self::DIRECTIVE_DELIMITER, $class);
        switch (count($_pieces)) {
            case 1:
                $class = $_pieces[0];
                $type = $class;
                break;
            case 2:
                $class = $_pieces[1];
                $type = $_pieces[0];
                break;
            case 0:
                throw new RuntimeException('class to load not specified');
                break;
            default:
                throw new RuntimeException('class directive not supported');
                break;
        }
        if (!isset($this->$class)) {
            $this->$class =& load_class($class, true, $type);
        }
        $this->is_loaded[] = $class;
        return $this->$class;
    }
}

