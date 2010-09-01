<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Model for the site, using MySQL
 * @version 1.0
 * @package Indexhibit++
 * @author Peng Wang <peng@pengxwang.com>
 **/

class Db extends MySQLDriver
{
    const OBJECT_EXHIBIT = 'exhibit';
    
    public function __construct () 
    {
        parent::__construct();
    }
    
    /**
     * @todo loosely couple column names
     * @return array
     **/
    public function get_sections () 
    {
        return $this->selectArray('section', array(), null, 
            array('secid', 'section', 'sec_desc', 'sec_proj', 'sec_ord'), 'ORDER BY sec_ord ASC');
    }
    
    public function get_site_settings () 
    {
        return $this->selectArray('object_meta', 
            array('obj_ref_type' => self::OBJECT_EXHIBIT), self::FETCH_RECORD);
    }
    
    public function get_cms_modes () 
    {
        return array(
            'on' => 1,
            'off' => 0
        );
    }
    
    public function get_site_nav_methods () 
    {
        return array(
            'chronological' => 1,
            'sectional' => 2
        );
    }
}
