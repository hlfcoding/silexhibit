<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Core class
 * Implements global function load_class() and stores the results
 * @version 1.1
 * @package Indexhibit
 * @author Vaska
 * @author Peng Wang <peng@pengxwang.com>
 **/
 
class Core
{
    public $is_loaded;
    // Declarations are added to clarify all classes that get loaded by 
    // core instances throughout the project, since its design specification vaguely
    // doesn't drawly where it stands between a small class and a god class,
    // and certainly there's no need to contain all the classes in the framework.
    public $db;
    public $lang;
    public $template;
    public $mustache;
    public $access;
    public $front;
    public $organize;
    
    const DIRECTIVE_DELIMITER = '.';
    
    public function __construct()
    {
        $this->load_class('db');
    }
    
    /**
     * Loads many classes
     * @param array
     * @return void
     **/
    public function load_classes ($classes) 
    {
        foreach ($classes as $class) {
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

