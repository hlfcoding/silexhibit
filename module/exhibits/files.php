<?php if (!defined('SITE')) exit('No direct script access allowed');
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
