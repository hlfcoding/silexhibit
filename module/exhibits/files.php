<?php if (!defined('SITE')) exit('No direct script access allowed');


function getSection($section='', $name, $attr='')
{
    global $default;
    
    $OBJ =& get_instance();
    
    $s = '';

    $rs = $OBJ->db->fetchArray("SELECT secid,section,sec_desc,sec_proj FROM ".PX."sections ORDER by sec_ord ASC");

    foreach ($rs as $a) 
    {
        //($section === $a['secid']) ? $sl = "selected ": $sl = "";
        $s .= option($a['secid'], $a['sec_desc'], $a['sec_proj'], 1);
    }

    return select($name, attr($attr), $s);
}


function getSections()
{
    global $default;
    
    $OBJ =& get_instance();
    
    $s = '';

    $rs = $OBJ->db->fetchArray("SELECT secid, sec_desc, sec_proj, sec_ord 
        FROM ".PX."sections ORDER by sec_ord ASC");

    if (!$rs)
    {
        return p('None');
    }
    
    // we need the highest number section
    // sorted by order, so we can array pop
    $quantities = $rs;
    $quantity = array_pop($quantities);

    foreach ($rs as $a) 
    {
        $projects = ($a['sec_proj'] == 1) ? '&nbsp;&nbsp;XX' : '';
        
        $s .= div(href($OBJ->lang->word('edit'), "?a=exhibits&q=section&id=$a[secid]") . '&nbsp;&nbsp;' . $a['sec_desc'] . $projects, "class='section'");
    }
    
    $s .= p(href($OBJ->lang->word('create new section'), '#', "onclick=\"toggle('add-sec'); return false;\""));
    
    $input = label($OBJ->lang->word('section name') . ' ' . span($OBJ->lang->word('required'), "class='small-txt'")) . input('sec_desc', 'text', "maxlength='25'", null);
    $input .= label($OBJ->lang->word('folder name') . ' ' . span($OBJ->lang->word('required'), "class='small-txt'")) . input('section', 'text', "maxlength='15'", null);
    $input .= input('hsec_ord', 'hidden', null, $quantity['sec_ord']);
    $input .= div(input('add_sec', 'submit', null, $OBJ->lang->word('add section')), "style='text-align: right;'");
    
    $s .= div($input, "style='display:none; margin-top: 9px;' id='add-sec'");

    return div($s, "class='sections'");
}


function getSectionOrd($section='', $name, $attr='')
{
    $OBJ =& get_instance();
    
    $s = '';

    $rs = $OBJ->db->fetchArray("SELECT sec_ord,sec_desc FROM ".PX."sections ORDER BY sec_ord ASC");

    foreach ($rs as $a) 
    {
        $s .= option($a['sec_ord'], $a['sec_ord'], $section, $a['sec_ord']);
    }

    return select($name, attr($attr), $s);
}


function getProcessing($state, $name, $attr)
{
    $OBJ =& get_instance();
    
    if ($state === '') $state = 0;
    
    $s = option(1, $OBJ->lang->word('on'), $state, 1);
    $s .= option(0, $OBJ->lang->word('off'), $state, 0);
    
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


function getOrganize($state, $name, $attr)
{
    $OBJ =& get_instance();
    
    if ($state === '') $state = 1;
    
    $s = option(1, $OBJ->lang->word('chronological'), $state, 1);
    $s .= option(2, $OBJ->lang->word('sectional'), $state, 2);
    
    return select($name, attr($attr), $s);  
}


function getThemes($path, $default)
{
    $modules = array();
    if (is_dir($path)) {
        if ($fp = opendir($path)) {
            while (($module = readdir($fp)) !== false) {
                if (preg_match('/^(_|CVS$)/i', $module) === 0 &&
                    preg_match('/\.(php|html)$/i', $module) === 0 &&
                    preg_match('/\.(|DS_Store)$/i', $module) === 0 &&
                    preg_match('/plugin|css|js|img/', $module) === 0) {
                    $modules[] = $module;
                }
            } 
        }
        closedir($fp);
    }
 
    sort($modules);
    
    $s = '';
    
    foreach ($modules as $module)
    {
        $s .= option($module, ucwords($module), $module, $default);
    }
    
    return select('obj_theme', null, $s);
}


function getPresent($path, $default)
{
    $OBJ =& get_instance();
    
    if ($OBJ->object['obj_mode'] == 1)
    {
        $modules = array();
        if (is_dir($path)) {
            if ($fp = opendir($path)) {
                while (($module = readdir($fp)) !== false) {
                    if (preg_match('/^exhibit/i', $module) === 0) {
                        $modules[] = $module;
                    }
                } 
            }
            closedir($fp);
        }
        sort($modules);
        $s = '';
        foreach ($modules as $module)
        {
            $search = array('exhibit.','.php');
            $replace = array('','');
            $module = str_replace($search, $replace, $module);
            $name = str_replace('_', ' ', $module);

            $s .= option($module, ucwords($name), $module, $default);
        }
    }
    else
    {
        global $default;
        
        // easy mode - defaults
        $formats = $default['standard_formats'];
        
        $s = '';
        foreach ($formats as $format)
        {
            if (file_exists($path . '/exhibit.' . $format . '.php'))
            {
                $name = str_replace('_', ' ', $format);
                $s .= option($format, ucwords($name), $format, $default);
            }
        }
        
        // we should throw an error if no formats exist
    }
    
    
    return select('obj_present', "id='ajx-present'", $s);
}


function createFileBox($num)
{
    $OBJ =& get_instance();
    
    $s = label($OBJ->lang->word('image title') . span(' ' . $OBJ->lang->word('optional')));
    
    for ($i = 0; $i <= $num; $i++)
    {
        ($i > 0) ? $style = " style='display:none'" : $style = '';
        
        $s .= div(input("media_title[$i]", 'text', null, null).'&nbsp;'.
            input("filename[]", 'file', null, null),
            "class='attachFiles' id='fileInput$i'$style");
    }   
    
    $s .= p(href($OBJ->lang->word('attach more files'), 'javascript:AddFileInput()'),
            "class='attachMore' id='attachMoreLink'");

    return $s;
}


function getExhibitImages($id)
{
    $OBJ =& get_instance();
    
    $body = "<ul id='boxes'>\n";
    
    // the images
    $imgs = $OBJ->db->fetchArray("SELECT * 
        FROM ".PX."media 
        WHERE media_ref_id = '$id'
        AND media_obj_type = '".OBJECT."'
        ORDER BY media_order ASC, media_id ASC");
    
    if ($imgs)
    {
        foreach ($imgs as $img)
        {
            $body .= "<li class='box' id='box$img[media_id]'><img src='" . BASEURL . GIMGS . "/sys-$img[media_file]' width='75' title='$img[media_title]' /><br /><a href='#' onclick=\"getImgPreview($img[media_id]); return false;\">".$OBJ->lang->word('edit')."</a></li>\n";
        }
    }
    else
    {
        $body .= "<li>".$OBJ->lang->word('no images')."</li>\n";
    }
    
    $body .= "</ul>\n";
    
    $body .= "<div class='cl'><!-- --></div>\n";
    
    return $body;
}


function getOnOff($input='', $attr='')
{
    $OBJ =& get_instance();
    $onoff = array('on' => 1, 'off' => 0);
    
    $li = '';
    $input = ($input === '') ? 'off' : $input;
    
    foreach($onoff as $key => $val)
    {
        $active = ($input === $val) ? "class='active'" : '';
        $extra = ($val === 0) ? "id='off'" : '';
        $li .= li($OBJ->lang->word($key), "$active title='$val' $extra");
    }
    
    return ul($li, $attr);
}


function getThumbSize($input='', $attr='')
{
    $OBJ =& get_instance();
    global $default;
    
    $li = '';
    $input = ($input === '') ? 100 : $input;
    
    foreach($default['thumbsize'] as $key => $size)
    {
        $active = ($input === $size) ? "class='active'" : '';
        $li .= li($OBJ->lang->word($key) . 'px', "$active title='$size'");
    }
    
    return ul($li, $attr);
}

function getImageSizes($input='', $attr='')
{
    $OBJ =& get_instance();
    global $default;
    
    $li = '';
    $input = ($input === '') ? 300 : $input;
    
    foreach($default['imagesize'] as $key => $size)
    {
        $title = $key . 'px';
        
        $active = ($input === $size) ? "class='active'" : '';
        $li .= li($title, "$active title='$size'");
    }
    
    return ul($li, $attr);
}


// this should be moved to a helper file
// for use with javascripts encodeURIComponent()
function utf8Urldecode($value)
{
    if (is_array($value))
    {
        foreach ($key as $val) { $value[$key] = utf8Urldecode($val); }
    }
    else
    {
        $value = urldecode($value);
    }

    return $value;
}


function deleteImage($file, $ext='')
{
    if ($file)
    {
        $file = ($ext === '') ? $file : $ext .'-' . $file;

        if (file_exists(DIRNAME . GIMGS . '/' . $file))
        {
            @unlink(DIRNAME . GIMGS . '/' . $file);
        }
    }
}


function getBreak($default)
{
    $s = '';
    
    for ($i = 0; $i <= 10; $i++)
    {
        $s .= option($i, $i, $i, $default);
    }
    
    return select('break', "id='ajx-break'", $s);
}


function getYear($init)
{
    global $default;
    $s = '';
    
    $this_year = date('Y');
    
    if ($init === '') $init = $this_year; // default is this year
    
    $current = $this_year + 1; // we want to add one year in the future
    
    for ($i = $current; $i >= $default['first_year']; $i--)
    {
        $s .= option($i, $i, $i, $init);
    }
    
    return select('year', "id='ajx-year'", $s); 
    
}


function getColorPicker($bgcolor)
{
    return "<div style='margin: 3px 0 5px 0;' onclick=\"toggle('plugin'); return false;\">
        <span id='plugID' style='background: #$bgcolor; cursor: pointer;'>&nbsp;</span> 
        <span id='colorTest2'>#$bgcolor</span>
    </div>
    
    <div id='plugin' onmousedown=\"HSVslide('drag','plugin',event);\" style='display: none;'>
        <div id='SV' onmousedown=\"HSVslide('SVslide','plugin',event);\" title='Saturation + Value'>
            <div id='SVslide' style='TOP: -4px; LEFT: -4px;'><br /></div>
        </div>
        
        <div id='H' onmousedown=\"HSVslide('Hslide','plugin',event);\" title='Hue'>
            <div id='Hslide' style='TOP: -7px; LEFT: -8px;'><br /></div>
            <div id='Hmodel'></div>
        </div>
    </div>
    
    <input id='colorTest' type='text' name='color' value='ffffff' style='display:none;' />\n\n";
}

