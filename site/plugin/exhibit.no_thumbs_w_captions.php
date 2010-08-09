<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* No thumbnails, with captions
*
* Exhbition format
* 
* @version 1.0
* @author Vaska 
*/

// defaults from the general libary - be sure these are installed
$exhibit['dyn_css'] = dynamicCSS();
$exhibit['exhibit'] = createExhibit();


function createExhibit()
{
    $OBJ =& get_instance();
    global $rs, $exhibit;
    
    $pages = $OBJ->db->fetchArray("SELECT * 
        FROM ".PX."media, ".PX."objects_prefs, ".PX."objects  
        WHERE media_ref_id = '$rs[id]' 
        AND obj_ref_type = 'exhibit' 
        AND obj_ref_type = media_obj_type 
        AND id = '$rs[id]' 
        ORDER BY media_order ASC, media_id ASC");
        
    // ** DON'T FORGET THE TEXT ** //
    $s = $rs['content'];
    $s .= "\n<div class='cl'>&nbsp;</div>\n";

    if (!$pages) return $s;
    
    $i = 1; $a = '';
    foreach ($pages as $go)
    {
        $title      = ($go['media_title'] == '') ? '&nbsp;' : $go['media_title'];
        $caption    = ($go['media_caption'] == '') ? '&nbsp;' : $go['media_caption'];
        
        if ($go['break'] != 0)
        {
            if ($i == $go['break'])
            {
                $i = 0;
                $break = "<div style='clear:left;'>&nbsp;</div>";
            }
            else
            {
                $break = '';
            }
        }
        else
        {
            $break = '';
        }
        
        $a .= "\n<span class='nothumb'><img src='".BASEURL.GIMGS."/$go[media_file]' alt='$title' title='$title' /><strong>$title</strong> $caption</span>$break\n";
        
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
    return ".nothumb { float: left; padding: 0 1px 9px 0;  }
    .nothumb img { display: block; margin: 0 0 1px 0; }
    .nothumb strong { display: block; }";
}
