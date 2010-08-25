<?php if (!defined('SITE')) exit('No direct script access allowed'); ?>

// --------------------------------------------------
// data layer tests
// --------------------------------------------------
// fetchRecord($query) 
// $OBJ->db->fetchRecord("SELECT * FROM {$tables['object']} WHERE id = 1")
-- <?php var_export($OBJ->db->fetchRecord("SELECT * FROM {$tables['object']} WHERE id = 1")) ?>


// fetchArray($query) 
// $OBJ->db->fetchArray("SELECT * FROM {$tables['section']} WHERE secid < 5")
-- <?php var_export($OBJ->db->fetchArray("SELECT * FROM {$tables['section']} WHERE secid < 5")) ?>


// selectArray($query) 
// $OBJ->db->selectArray($tables['section'], array('secid' => 8), 'array', 'section, sec_desc')
-- <?php var_export($OBJ->db->selectArray($tables['section'], array('secid' <= 8), 'array', 'section, sec_desc')) ?>


// getCount($query)
// $OBJ->db->getCount("SELECT * FROM {$tables['object']} WHERE id < 50")
-- <?php var_export($OBJ->db->getCount("SELECT * FROM {$tables['object']} WHERE id < 50")) ?>

    