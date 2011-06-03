<?php if (!defined('SITE')) exit('No direct script access allowed');

// modified indexhibit functions
//  chronological
//  sectional
//  front_*_css
//  front_*_js

// procedure
load_plugins(DIRNAME . BASENAME . '/site/plugin/', 'plugin');

// functions
// this file will grab all the plugin.$something.php and load it up
function load_plugins($path, $default)
{
    // let's get the folders and info...
    $modules = array();
    if (is_dir($path)) {
        if ($fp = opendir($path)) {
            while (($module = readdir($fp)) !== false) 
            {
                if (strpos($module, 'plugin', 0) === 0) {
                    $modules[] = $module;
                }
            } 
        }
        closedir($fp);
    }
    foreach ($modules as $load)
    {
        include_once $path . $load;
    }
    return;
}

function front_index()
{
    $OBJ =& get_instance();
    return $OBJ->front->front_index();
}

function front_exhibit()
{
    $OBJ =& get_instance();
    return $OBJ->front->front_exhibit();
}

function front_background()
{
    $OBJ =& get_instance();
    return $OBJ->front->front_background();
}

function front_lib_css()
{
    $OBJ =& get_instance();
    return $OBJ->front->front_lib_css();
}

function front_lib_js()
{
    $OBJ =& get_instance();
    return $OBJ->front->front_lib_js();
}

function front_dyn_css()
{
    $OBJ =& get_instance();
    return $OBJ->front->front_dyn_css();
}

function front_dyn_js()
{
    $OBJ =& get_instance();
    return $OBJ->front->front_dyn_js();
}

function getNavigation()
{
    global $rs;
    
    return ($rs['obj_org'] == 1) ? chronological() : sectional();
}

// chronological navigation type
function chronological()
{
    $OBJ =& get_instance();
    global $rs, $default;

    $pages = $OBJ->db->fetchArray("SELECT id, title, content, url, 
        section, sec_desc, sec_disp, year, secid, sec_proj     
        FROM ".PX."objects, ".PX."sections 
        WHERE status = '1' 
        AND hidden != '1' 
        AND section_id = secid  
        ORDER BY sec_ord ASC, year DESC, ord ASC");
        
    if (!$pages) return 'Error with pages query';
    
    foreach($pages as $record)
    {
        $record['content'] = truncate($record['content']);
        // two is our projects
        if ($record['sec_proj'] != 1) {
            $order[$record['sec_desc']][] = array(
                'id' => $record['id'],
                'title' => $record['title'],
                'preview' => $record['content'],
                'url' => $record['url'],
                'year' => $record['year'],
                'secid' => $record['secid'],
                'disp' => $record['sec_disp']);
        } else {
            $order[$record['year']][] = array(
                'id' => $record['id'],
                'title' => $record['title'],
                'preview' => $record['content'],
                'url' => $record['url'],
                'year' => $record['year'],
                'secid' => $record['secid'],
                'disp' => $record['sec_disp']);
        }
    }
    
    return section_list($order);
}

// sections navigation
function sectional()
{
    $OBJ =& get_instance();
    global $rs;
    $pages = $OBJ->db->fetchArray("SELECT id, title, content, url, 
        section, sec_desc, sec_disp, year, secid    
        FROM ".PX."objects, ".PX."sections 
        WHERE status = '1' 
        AND hidden != '1' 
        AND section_id = secid  
        ORDER BY sec_ord ASC, ord ASC");
        
    if (!$pages) {
        return 'Error with pages query';
    }
    foreach($pages as $record) 
    {
        $record['content'] = truncate($record['content']);
        $order[$record['sec_desc']][] = array(
            'id' => $record['id'],
            'title' => $record['title'],
            'preview' => $record['content'],
            'url' => $record['url'],
            'year' => $record['year'],
            'secid' => $record['secid'],
            'disp' => $record['sec_disp']);
    }
    return section_list($order);
}


// background image is fixed attachment
function backgrounder($color='', $img='', $tile='')
{
    if (($color == '') && ($img = '')) {
        return;
    }
    $style = (strtolower($color) != 'ffffff') ? "background-color: #$color;" : '';
    $tile = ($tile != 1) ? 'no-repeat' : 'repeat';
    $style .= ($img != '') ? "\nbackground-image: url(".BASEURL."/files/$img);\nbackground-repeat: $tile;\nbackground-position: 215px 0;\nbackground-attachment: fixed;\n" : '';
    // nothing to add
    if ($style == '') {
        return;
    }
    return "<style type='text/css'>\nbody { $style }\n</style>";
}


function ndxz_users()
{
    $REST =& load_class('rest', TRUE, 'lib');
    return $REST->indexhibit_user_list();
}


function section_list($order) 
{
    global $rs;
    $s = '';
    foreach($order as $key => $out)
    {
        $count = count($out);
        $s .= "<ul>\n";
        if ($out[0]['disp'] == 1) {
            $s .= "<li class='section-title'><span class='h-main'><span class='title'>$key</span> <sup>($count)</sup></span></li>\n";
        }
        foreach($out as $page)
        {
            $active = ($rs['id'] == $page['id']) ? " class='active'" : '';
                
            $s .= "<li$active><a href=\"" 
                . BASEURL . ndxz_rewriter($page['url']) 
                . "\" title=\"{$page['preview']}\""
                . " onclick=\"do_click();\">" 
                . $page['title'] 
                . "</a></li>\n";
        }
        $s .= "</ul>\n\n";
    }
    return $s;
}    

include 'index.custom.php';