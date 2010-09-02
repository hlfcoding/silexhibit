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
     * @todo use prefixes
     * @todo use joins
     * @param int 
     * @return array
     **/
    public function get_exhibit_by_id ($id) 
    {
        return $this->selectArray(array('object', 'object_meta', 'section'), array(
            'id' => $id,
            'object' => self::OBJECT_EXHIBIT,
        ), self::FETCH_RECORD, '', 'AND section_id = secid AND object = obj_ref_type');
    }
    
    public function get_exhibit_images_by_id ($id) 
    {
        return $this->selectArray('media', array(
            'media_ref_id' => $id,
            'media_obj_type' => self::OBJECT_EXHIBIT,
        ), null, '', 'ORDER BY media_order ASC, media_id ASC');
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
        
    public function get_site_nav_methods () 
    {
        return array(
            'chronological' => 1,
            'sectional' => 2
        );
    }
    
    public function get_cms_modes () 
    {
        return $this->get_on_off();
    }

    public function get_exhibit_statuses ()
    {
        return $this->get_on_off();
    }
    
    public function get_on_off ()
    {
        return array(
            'on' => 1, 
            'off' => 0
        );
    }
}
