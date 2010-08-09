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
    var $is_loaded;
    
    /**
    * Returns loaded database object or error
    *
    * @param void
    * @return array
    */
    function Core()
    {
        $this->load_db();
    }
    
    /**
    * Return language and core classes
    *
    * @param void
    * @return array
    */
    function auto_load()
    {
        $this->load_lang();
        $this->assign_core();
    }
    
    /**
    * Returns core classes
    *
    * @param void
    * @return array
    */
    function assign_core()
    {
        foreach (array('template','access') as $val)
        {
            $class = strtolower($val);
            if (!is_object($class)) $this->$class =& load_class($val, TRUE, 'lib');
            $this->is_loaded[] = $class;
        }
    }
    
    /**
    * Returns language file
    *
    * @param void
    * @return array
    */
    function load_lang()
    {
        $class = strtolower('lang');
        if (!is_object($class)) $this->$class =& load_class($class, TRUE, 'lang');
        $this->is_loaded[] = $class;
    }
    
    /**
    * Returns loaded database object or error
    *
    * @param void
    * @return array
    */
    function load_db()
    {
        $class = strtolower('db');
        if (!is_object($class)) $this->$class =& load_class($class, TRUE, 'db');
        $this->is_loaded[] = $class;
    }
    
    /**
    * Return loaded object
    *
    * @param string $class
    * @return array
    */
    function lib_class($class)
    {
        if ($class == '') return;
        
        $class = strtolower($class);
        if (!is_object($class)) $this->$class =& load_class($class, TRUE, 'lib');
        $this->is_loaded[] = $class;
    }
}

