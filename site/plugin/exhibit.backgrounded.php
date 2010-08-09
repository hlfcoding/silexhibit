<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Backgrounded
*
* Exhbition format
* 
* @version 1.0
* @author Vaska 
*/

// defaults from the general library - be sure these are installed
$exhibit['dyn_css'] = dynamicCSS();
$exhibit['dyn_js'] = dynamicJS();
$exhibit['exhibit'] = createExhibit();

function createExhibit()
{
    $OBJ =& get_instance();
    global $rs, $exhibit;
    
    $pages = $OBJ->db->fetchArray("SELECT * 
        FROM ".PX."media   
        WHERE media_ref_id = '$rs[id]' 
        ORDER BY media_order ASC, media_id ASC");
        
    // ** DON'T FORGET THE TEXT ** //
    $s = $rs['content'];
    $s .= "\n<div class='cl'>&nbsp;</div>\n";
    
    if (!$pages) return $s;
    
    $i = 0; $a = '';
    foreach ($pages as $go)
    {
        $title      = ($go['media_title'] == '') ? 'N/A' : $go['media_title'];
        $caption    = ($go['media_caption'] == '') ? 'N/A' : $go['media_caption'];
        
        $a .= "\n<span class='backgrounded'><a href='#' onclick=\"swapImg($i, '$go[media_file]');return false;\"><img src='" . BASEURL . GIMGS . "/sys-$go[media_file]' alt='$caption' title='$title' id='img$i' /></a></span>\n";
        
        $i++;
    }
    
    // images
    $s .= "<div id='img-container'>\n";
    $s .= $a;
    $s .= "</div>\n";
    
    $s .= "<div id='backgrounded-text'>&nbsp;</div>\n";
        
    return $s;
}


function dynamicCSS()
{
    return ".backgrounded { margin-right: 1px; }
    .backgrounded a { border: none; }
    .backgrounded a img { border: 3px solid #fff; height: 25px; width: 25px; }
    .backgrounded-text { margin-top: 9px; }";
}


function dynamicJS()
{
    global $rs;
    
    $tile = ($rs['tiling'] != 1) ? ", backgroundRepeat: 'no-repeat'" : '';
    
    return "function swapImg(a, image)
    {
        var the_path = '" . BASEURL . GIMGS ."/' + image;
        show = new Image;
        show.src = the_path;
        $('body').css({ backgroundImage: 'url(' + show.src + ')', backgroundPosition: '215px 0' $tile });

        var title = $('#img' + a).attr('title');
        var caption = $('#img' + a).attr('alt');
        
        if (title != 'N/A') 
        {
            caption = (caption != 'N/A') ? ': ' + caption : '';
            $('#backgrounded-text').html('<span style=\"background: white; line-height: 24px;\">' + title + caption + '</span>');
        }
        else
        {
            $('#backgrounded-text').html('');
        }
    }";
}


?>