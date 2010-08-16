<?php if (!defined('SITE')) exit('No direct script access allowed');


function getFiles()
{
    $OBJ =& get_instance();
    global $go;
    
    $s = '';

    $rs = $OBJ->db->fetchArray("SELECT * FROM ".PX."media 
        WHERE media_ref_id = '0' 
        AND media_obj_type = '' 
        ORDER by media_uploaded DESC");

    if (!$rs)
    {
        $s = 'No files yet';
    }
    else
    {
        foreach ($rs as $a) 
        {
            // fake 'mime', actually
            $mime = array_pop(explode('.', $a['media_file']));

            $use = span(filesManagerType($mime, $a['media_file'], $a['media_x'], $a['media_y'], $a['media_caption']),  "class='p-action'");
                
            $edit = span(href("<img src='asset/img/files-edit.gif' />",
                "?a=$go[a]&amp;q=editfile&amp;id=$a[media_id]"), "class='p-action'");
            
            $url = BASEURL . '/files/' . $a['media_file'];
            
            $s .= div($use . $edit . span(href($a['media_file'], $url, "target='show'"), "class='p-name'"),
                row_color("class='row-color'"));
        }
    }

    return $s;
}



function linksManager()
{
    $OBJ =& get_instance();
    
    $rs = $OBJ->db->fetchArray("SELECT title,url,sec_desc  
        FROM ".PX."objects 
        INNER JOIN ".PX."sections ON ".PX."objects.section_id = secid 
        WHERE status = '1' AND url != '' 
        ORDER BY section_id ASC");
        
    // rewrite the array based on section name
    $i = 0;
    $x = '';
    if (is_array($rs))
    {
        foreach ($rs as $ar)
        {
            $newarr[$ar['sec_desc']][$i] = array($ar['url'],$ar['title']);
            $i++;
        }
    }
        
    foreach ($newarr as $key => $out)
    {
        $p = '';
            
        foreach ($out as $go)
        {
            $p .= "<option value=\"<a href='".BASEURL.ndxz_rewriter($go[0])."' alt='' title='".htmlspecialchars($go[1])."'>$go[1]\">$go[1]</option>\n";
            
            // hackery
            //$p .= "<option value=\"<a href='".BASEURL.ndxz_rewriter($go[0])."' alt='' title='".htmlspecialchars($go[1])."'>$go[1]</a>\">$go[1]</option>\n";
        }
            
        $x .= "<optgroup label='".ucwords($key)."'>\n$p\n</optgroup>\n";
            
    }
        

    if (is_array($rs)) 
    {   
        $s = select('sysLink',"style='width:225px;'",$x);
    } 
    else
    {       
        $s = p($OBJ->lang->word('none found'));
    }
    
    return $s;
}



function filesManagerType($type, $file, $x='', $y='', $desc='')
{
    global $uploads;
    
    // images
    if (in_array($type, $uploads['images']))
    {
        return href("<img src='asset/img/files-use.gif' />", '#', "onClick=\"ModInsImg('". BASEURL . '/files/' . $file ."','$x','$y');return false;\"");
    }
    // mp3, mov, etc...
    elseif (in_array($type, $uploads['media']))
    {
        switch ($type) {
        case 'mp3':
           return href("<img src='asset/img/files-use.gif' />", '#', "onClick=\"ModInsMP3('". BASEURL . '/files/' . $file ."');return false;\"");
           break;
        case 'mov':
           return href("<img src='asset/img/files-use.gif' />", '#', "onClick=\"ModInsMov('". BASEURL . '/files/' . $file ."','$x','$y');return false;\"");
           break;
        // not in use...
        case 'avi':
           return href("<img src='asset/img/files-use.gif' />", '#', "onClick=\"ModInsAVI('". BASEURL . '/files/' . $file ."','$x','$y');return false;\"");
           break;
        case 'jar':
           return href("<img src='asset/img/files-use.gif' />", '#', "onClick=\"ModInsJAR('". BASEURL . '/files/' . $file ."','$x','$y');return false;\"");
           break;
        }
    }
    // flash
    elseif (in_array($type, $uploads['flash']))
    {
        return href("<img src='asset/img/files-use.gif' />", '#', "onClick=\"ModInsFlash('". BASEURL . '/files/' . $file ."','$x','$y');return false;\"");
    }
    // other files
    else
    {
        return href("<img src='asset/img/files-use.gif' />", '#', "onClick=\"ModInsFile('". BASEURL . '/files/' . $file ."','$desc');return false;\"");
    }   
}



function createFileBox($num)
{
    $OBJ =& get_instance();
    
    $s = label($OBJ->lang->word('title') . span(' ' . $OBJ->lang->word('optional')));
    
    for ($i = 0; $i <= $num; $i++)
    {
        ($i > 0) ? $style = " style='display:none'" : $style = '';
        
        $s .= div(input("media_title[$i]",'text',"size='20' maxlength='35'",null).'&nbsp;'.
            input("filename[$i]",'file',"size='20'",null),
            "class='attachFiles' id='fileInput$i'$style");
    }   
    
    $s .= p(href($OBJ->lang->word('attach more'),'javascript:AddFileInput()')
            ,"class='attachMore' id='attachMoreLink'");

    return $s;
}



function getTimeOffset($default='', $name, $attr='')
{
    $s = '';
    $default = ($default === '') ? 0 : $default;
    $timestamp = getNow();

    $offset = array(13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0,
        -1, -2, -3, -4, -5, -6, -7, -8, -9, -10, -11, -12);
    
    $timestamp = str_replace(array('-', ':', ' '), array('', '', ''), $timestamp);
    
    $time[0] = substr($timestamp, 8, 2); // hours
    $time[1] = substr($timestamp, 10, 2); // min
    $time[2] = substr($timestamp, 12, 2); // seconds
    $time[3] = substr($timestamp, 6, 2); // day
    $time[4] = substr($timestamp, 4, 2); // month
    $time[5] = substr($timestamp, 0, 4); // year

    foreach ($offset as $a) 
    {
        $hello = date('Y-m-d H:i:s', mktime($time[0]+$a, $time[1], $time[2], $time[4], $time[3], $time[5]));
        
        $newdate = strftime("%A, %R %p", strtotime($hello));
        
        ($default === $a) ? $sl = "selected ": $sl = "";
        $s .= option($a, $newdate, $default, $a);
    }

    return select($name, attr($attr), $s);
}


function getTimeFormat($default='', $name, $attr='')
{
    $s = '';
    $default = ($default === '') ? '%Y-%m-%d %T' : $default;
    $timestamp = getNow();

    $formats = array('%d %B %Y', '%A, %H:%M %p', '%Y-%m-%d %T');
    
    $timestamp = str_replace(array('-', ':', ' '), array('', '', ''), $timestamp);
    
    $time[0] = substr($timestamp, 8, 2); // hours
    $time[1] = substr($timestamp, 10, 2); // min
    $time[2] = substr($timestamp, 12, 2); // seconds
    $time[3] = substr($timestamp, 6, 2); // day
    $time[4] = substr($timestamp, 4, 2); // month
    $time[5] = substr($timestamp, 0, 4); // year

    foreach ($formats as $format) 
    {
        $hello = date('Y-m-d H:i:s', mktime($time[0], $time[1], $time[2], $time[4], $time[3], $time[5]));
        
        $newdate = strftime($format, strtotime($hello));
        
        ($default === $format) ? $sl = "selected ": $sl = "";
        $s .= option($format, $newdate, $default, $format);
    }

    return select($name, attr($attr), $s);
}


function getLanguage($default='', $name, $attr='')
{
    $OBJ =& get_instance();
    
    $s = '';

    $rs = $OBJ->lang->lang_options();

    if ($default === '')
    {
        $s .= option('', $OBJ->lang->word('make selection'), 0, 0);
    }

    foreach ($rs as $key => $a) 
    {
        $language = array_pop($a);
        
        // check to see if the lang folder exists
        if (is_dir(DIRNAME . BASENAME . '/' . LANGPATH . '/' . $key))
        {
            ($default === $a) ? $sl = "selected ": $sl = "";
            $s .= option($key, $OBJ->lang->word($language), $default, $key);
        }
    }
    clearstatcache();

    return select($name, attr($attr), $s);
}


function getGeneric($state, $name, $attr)
{
    $OBJ =& get_instance();
    
    if ($state === '') $state = 0;
    
    $s = option(1, $OBJ->lang->word('on'), $state, 1);
    $s .= option(0, $OBJ->lang->word('off'), $state, 0);
    
    return select($name, attr($attr), $s);  
}
