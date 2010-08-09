<?php if (!defined('SITE')) exit('No direct script access allowed');


/**
* Router class
*
* Helps us get from url to the correct class and method
* 
* @version 1.0
* @author Vaska 
*/
class Router extends Core
{
    var $method;
    var $go;


    /**
    * Returns $go array from $_GET values and validates
    *
    * @param void
    * @return array
    */
    function Router()
    {
        // don't access this space directly
        // work above or below it
        parent::Core();
        
        // hackish so the front end will work
        $this->auto_load();
        
        // from entrance helper - sets defaults
        directions(); // global $go of default $_GET values
        $this->check_routes();
    }
    
    /**
    * Returns null or loads error procedure
    *
    * @param void
    * @return mixed
    */
    function check_routes()
    {
        global $go, $default;
        
        $modules = array();

        if ($fp = @opendir('module')) 
        {
            while (($module = readdir($fp)) !== false)
            {
                if ((!eregi("^_",$module)) && (!eregi("^CVS$",$module)) && (!eregi(".php$",$module)) && (!eregi(".html$",$module)) && (!eregi(".DS_Store",$module)) && (!eregi("\.",$module)))
                {      
                    $modules[] = $module;
                }
            } 
        }   

        closedir($fp); 
        sort($modules);
        
        // check if the 'class' route exists - default
        if (!in_array($go['a'], $modules)) show_error('router err 1');
        
        return;
    }
    
    
    /**
    * Return boolean
    *
    * @param array $method
    * @return boolean
    */
    // review this later
    function get_method($methods)
    {
        if ((!is_array($_POST)) || (!is_array($methods))) return FALSE;
        
        foreach ($methods as $method)
        {
            if (isset($_POST[$method]))
            {
                $this->method = $method;
                return TRUE;
            }
        }
    }
    
    
    /**
    * Returns callback'd function results
    *
    * @param array $methods
    * @param array $library
    * @return string
    */
    function posted($methods, $library)
    {
        if (isset($_POST) && $this->get_method($library))
        {
            if (method_exists($methods, 'sbmt_' . $this->method)) 
            {
                return call_user_func(array(&$methods, 'sbmt_' . $this->method));
            }
        }
        else
        {
            return;
        }
    }
    
    
    /**
    * Returns callback or error
    *
    * @param object $INDX
    * @param string $class
    * @param string $method
    * @return mixed
    */
    // where do we want to do now?
    function tunnel(&$INDX, $class, $method)
    {
        if (method_exists($INDX, 'page_' . $method))
        {
            call_user_func(array(&$INDX, 'page_' . $method), NULL);
        }
        else
        {
            show_error('router err 1');
        }
    }
}
