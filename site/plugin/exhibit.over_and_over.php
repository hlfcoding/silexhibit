<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Over and over
*
* Exhbition format
* 
* @version 1.0
* @author Peng Wang
* @author Vaska 
*/

// defaults from the general libary - be sure these are installed
$exhibit['dyn_css'] = dynamicCSS();
$exhibit['exhibit'] = createExhibit();


function createExhibit()
{
    $OBJ =& get_instance();
    global $rs;
    
    $pages = $OBJ->db->fetchArray("SELECT * 
        FROM ".PX."media, ".PX."objects_prefs 
        WHERE media_ref_id = '$rs[id]' 
        AND obj_ref_type = 'exhibit' 
        AND obj_ref_type = media_obj_type 
        ORDER BY media_order ASC, media_id ASC");
        
    // ** DON'T FORGET THE TEXT ** //
    $s = $rs['content'];
    $s .= "\n<div class='cl'>&nbsp;</div>\n";

    if (!$pages) return $s;
    
    $i = 1; $a = '';
    
    // people will probably want to customize this up
    foreach ($pages as $go)
    {
        $text = ($go['media_title'] == '') ? '' : $go['media_title'];
        $text .= ($go['media_caption'] == '') ? '&nbsp;' : ': ' . $go['media_caption'];
        
        $a .= "\n<p class='scrollItem'><img src='" . BASEURL . GIMGS . "/$go[media_file]' alt='$go[media_caption]' /><br />\n<span>$text</span>\n</p>\n";
        
        $i++;
    }
    
    // images
    $s .= "<div id='img-container'>\n";
    $s .= $a;
    $s .= "</div>\n";
        
    return $s;
}


function dynamicCSS()
{
    return "#img-container p { margin-bottom: 18px; }\n#img-container p span { line-height: 18px; }";
}

