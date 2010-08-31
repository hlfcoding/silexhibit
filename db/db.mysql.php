<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Model for the site, using MySQL
 * @version 1.0
 * @package Indexhibit++
 * @author Peng Wang <peng@pengxwang.com>
 **/

class Db extends MySQLDriver
{
    
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
            array('secid', 'section', 'sec_desc', 'sec_proj'), 'ORDER BY sec_ord ASC');
    }
}
