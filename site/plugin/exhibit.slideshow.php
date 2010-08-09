<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Slideshow
*
* Exhibition format
* 
* @version 1.0 
* @author Peng Wang
* @author Simon Lagneaux 
* @author Vaska
*/


// defaults from the general libary - be sure these are installed
$exhibit['dyn_css'] = dynamicCSS();
$exhibit['lib_js'] = array('jquery.cycle.all.js');
$exhibit['dyn_js'] = dynamicJS();
$exhibit['exhibit'] = createExhibit();


function dynamicJS()
{
    global $timeout;
    return "$(document).ready(function(){ 
    $('#s1').cycle({
    fx:'fade', 
    speed:'2000', 
    timeout: 0, 
    next:'#next', prev:'#prev'});
    });";
}


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

    if (!$pages) return $s;
    
        $i = 1; $a = '';
    
    // people will probably want to customize this up
    $wrap_title     = '<span class="title">%s</span>';
    $wrap_caption   = '<span class="caption">%s</span>';
    foreach ($pages as $go)
    {
        $title 		= ($go['media_title'] == '') ? '' : sprintf($wrap_title, ($go['media_title'] . '&nbsp;'));
        $caption 	= ($go['media_caption'] == '') ? '' : sprintf($wrap_caption, ($go['media_caption']));
        //$x = getimagesize(BASEURL . GIMGS . '/' . $go['media_file']);
        
        $a .= "\n<div class='slide'><img src='" . BASEURL . GIMGS . "/$go[media_file]' class='img-bot' />";
        $a .= ( ! empty($title) OR ! empty($caption)) ? "<p class='meta'>{$title}{$caption}</p>" : '';
        $a .= "</div>\n";
        
        $i++;
    }
    
    $nav_title = '&darr; Gallery';
    $nav_prev = '&larr; Prev';
    $nav_next = 'Next &rarr;';
    $nav_format = '%1$s %4$s &nbsp; %2$s | %3$s';
    // images
    $s .= "<div id='img-container' class='slideshow-plugin'>\n";
    $s .= sprintf("<p class='nav'>{$nav_format}</p>"
        , $nav_title
        , "<a id='prev' class='btn' href='#'>{$nav_prev}</a>"
        , "<a id='next' class='btn' href='#'>{$nav_next}</a>"
        , "<span id='num'></span>"
        );
    $s .= "<div id='s1' class='pics'>\n";
    $s .= $a;
    $s .= "</div>\n";
    $s .= "</div>\n\n";
        
    return $s;
}


function dynamicCSS()
{
    return "#num {padding-left: 6px;}
    .img-bot {margin-bottom: 6px; display: block; }";
}

