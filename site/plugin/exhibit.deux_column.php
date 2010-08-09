<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Dexu column
*
* Exhbition format
* inspired by miaandjem.com
* 
* @version 1.0
* @author Vaska 
*/

// defaults from the general libary - be sure these are installed
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
    
    if ($pages)
    {
        $i = 0; $a = '';
        foreach ($pages as $go)
        {
            $title      = ($go['media_title'] == '') ? 'N/A' : $go['media_title'];
            $caption    = ($go['media_caption'] == '') ? 'N/A' : $go['media_caption'];
            
            $png        = ($go['media_mime'] == 'png') ? " class='png'" : '';
        
            $a .= "\n<a href='#' onclick=\"swapImg($i, '$go[media_file]');return false;\"><img src='" . BASEURL . GIMGS . "/th-$go[media_file]' alt='$caption' title='$title' id='img$i'$png /></a>\n";
        
            $i++;
        }
        
        $a .= "\n<p class='text'><a href='#' onclick=\"swapText();return false;\">Text</a></p>\n";
    }
    
    // images
    $s .= "<div id='img-container'>\n";
    $s .= "<div id='d-col1'>\n";
    $s .= $a;
    $s .= "</div>\n";
    
    // text
    $s .= "<div id='d-col2'>\n";
    $s .= $rs['content'];
    $s .= "</div>\n";
    
    $s .= "<div id='hidden-text'>\n";
    $s .= $rs['content'];
    $s .= "</div>\n";
    
    $s .= "\n<div class='cl'>&nbsp;</div>\n";
    $s .= "</div>\n";
        
    return $s;
}


function dynamicCSS()
{
    return "#d-col1 { float: left; width: 200px; }
    #d-col2 { margin-left: 205px; }
    #d-col1 img { padding-bottom: 12px; }
    #hidden-text { display: none; }";
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
        
        var img = '<img src=' + the_path + ' />';
        var title = $('#img' + a).attr('title');
        var caption = $('#img' + a).attr('alt');
        
        if (title != 'N/A') 
        {
            caption = (caption != 'N/A') ? ': ' + caption : '';

            img = img + '<br /><span>' + title + caption + '</span>';
        }
        
        $('#d-col2').html(img);
    }
    
    function swapText()
    {
        var text = $('#hidden-text').html();
        $('#d-col2').html(text);
    }";
}

