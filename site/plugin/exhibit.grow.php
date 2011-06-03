<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Grow
*
* Exhbition format
* 
* @version 1.0
* @author Vaska 
* @author Daniel Eatock
*/

// defaults from the general libary - be sure these are installed
$exhibit['dyn_css'] = dynamicCSS();
$exhibit['lib_js'] = array('grow.vaska.js');
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
    
    
    $thumb = $rs['thumbs'] + 20;
    
    $i = 1; $j = 0; $a = '';

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

        
        $a .= "<div class='grow' id='img$j' style='float: left;'>\n";
        $a .= "<div class='thumb'>\n";
            $a .= "<div class='thumb-it'>\n";
                $a .= "<a href='#' class='thumb-img'   onclick=\"toggleImg($j,$go[media_x],$go[media_y],$go[thumbs]);return false;\"><img src='" . BASEURL . GIMGS . "/th-$go[media_file]' alt='$go[media_file]' title='$title' /></a>\n";
            $a .= "</div>\n";

            $a .= "</div>\n";
            $a .= "<strong>&nbsp;${title}&nbsp;</strong>\n<em>&nbsp;${caption}&nbsp;</em>\n";
          $a .= "</div>\n\n";
        //}
        
        $i++; $j++;
    }
    
    // images
    $s .= "<!--[if lte IE 6]><style type='text/css'>.thumb-img { top: -50%; }</style><![endif]-->\n\n";
    $s .= "<div id='img-container'>\n";
    $s .= $a;
    $s .= "</div>\n";
        
    return $s;
}


function dynamicCSS()
{
    global $rs;
    
    // for .thumb-it we need #top: -50%; for IE...but it doesn't work.

    $thumb = $rs['thumbs'] + 20;
    
    return ".grow { float:left; margin-bottom: 18px; }
    .thumb {  padding:0; text-align:left; display: table; height: ${thumb}px; position: relative; overflow: hidden; width: ${thumb}px; }

    .grow strong { margin-top: 3px; text-align: left; display: block; font-weight: normal; text-align: center; }
    .grow em { font-style: normal; display: none; text-align: center; }
    
    .thumb-it { position: absolute; display: table-cell; vertical-align: middle; text-align: center; }
    .thumb-img { position: relative; width: ${thumb}px; display:block; }";
}

