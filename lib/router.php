<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Router class
 * Helps us get from url to the correct class and method
 * @version 1.1
 * @package Indexhibit
 * @author Vaska 
 * @author Peng Wang <peng@pengxwang.com>
 **/
class Router extends Core
{
    public $method;
    public $go;
    protected $modules;

    public function __construct ()
    {
        // don't access this space directly
        // work above or below it
        parent::__construct();
        $this->load_classes(array('lang', 'lib.template', 'lib.access'));
        // from entrance helper - sets defaults
        directions(); // global $go of default $_GET values
        $this->check_routes();
    }
    
    /**
     * Checks to see if admin class exists
     * @todo figure out exactly what this does, and why a global is used
     */
    protected function check_routes ()
    {
        global $go, $default;
        if (isset($this->modules)) {
            return;
        }
        $this->modules = array();
        if ($fp = @opendir('module')) {
            while (($module = readdir($fp)) !== false) {
                if (preg_match('/^(_|CVS$)/i', $module) === 0 &&
                    preg_match('/\.(php|html)$/i', $module) === 0 &&
                    preg_match('/\.(|DS_Store)$/i', $module) === 0) {
                    $this->modules[] = $module;
                }
            } 
        }   
        closedir($fp); 
        sort($this->modules);
        // check if the 'class' route exists - default
        if (!in_array($go['a'], $this->modules)) {
            show_error('router err 1');
        }
        return;
    }
    
    /**
     * Return boolean
     * @param array<string>
     * @return boolean
     * @todo review
     **/
    public function get_method ($methods)
    {
        if ((!is_array($_POST)) || (!is_array($methods))) {
            return false;
        }
        foreach ($methods as $method) {
            if (isset($_POST[$method])) {
                $this->method = $method;
                return true;
            }
        }
    }
    
    /**
     * Returns callback'd function results
     *
     * @param array<string>
     * @param string
     * @return ?
     **/
    public function posted ($methods, $library)
    {
        if (isset($_POST) && $this->get_method($library)) {
            if (method_exists($methods, 'sbmt_' . $this->method)) {
                return call_user_func(array(&$methods, 'sbmt_' . $this->method));
            }
        } else {
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
     **/
    function tunnel(&$INDX, $class, $method) {
        if (method_exists($INDX, 'page_' . $method)) {
            call_user_func(array(&$INDX, 'page_' . $method), null);
        } else {
            show_error('router err 1');
        }
    }
}
