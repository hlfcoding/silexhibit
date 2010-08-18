<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Custom helper functions that mainly extend on the frontend. 
 * @see /ndxz-studio/site/plugin/index.php
 * @author Peng Wang <peng@pengxwang.com>
 * @version 1
 */

function antispambot ($address, $mailto = 0)
{
    $safe = '';
    srand((float) microtime() * 1000000);
    for ($i = 0; $i < strlen($address); $i++) 
    {
        $j = floor(rand(0, 1 + $mailto));
        switch ($j) 
        {
            case 0:
                $safe .= '&#' . ord(substr($address, $i, 1)) . ';';
            break; case 1:
                $safe .= substr($address, $i, 1);
            break; case 2:
                $safe .= '%' . sprintf('%0' . 2 . 's', dechex(ord(substr($address, $i, 1))));
            break; 
        }
    }
    $safe = str_replace('@', '&#64;', $safe);
    return $safe;
}

function load_xml ($path = '', $namespace = '') 
{
    global $rs;
    $path = DIRNAME . BASENAME . DS . "$path.xml";
    if (!file_exists($path)) {
        throw new RuntimeException("Failed to open xml file: $filename");
        return;
    }
    $data = simplexml_load_file($path);
    foreach ($data->children() as $key => $value) 
    {
        $rs["${namespace}_${key}"] = trim($value, " \n");
    }
}

function add_globals ($namespace = 'custom')
{
    global $rs;
    
    $rs["${namespace}_html_validation"] = 'http://validator.w3.org/check/referer';
    $rs["${namespace}_css_validation"] = 'http://jigsaw.w3.org/css-validator/validator?'
         . htmlentities(
             http_build_query(array(
                 'profile' => 'css21', 
                 'warning' => 0, 
                 'uri' => ''
             ))
         ) .  str_replace('http://', '', FULLURL);
    $rs["${namespace}_full_url"] = FULLURL;
    $info = _get_browser();
    $rs["${namespace}_browser"] = strtolower($info['browser']);
    $rs["${namespace}_browser_and_version"] = strtolower($info['browser']) . $info['majorVersion'];
    $rs["${namespace}_browser_platform"] = strtolower($info['platform']);    
    $rs["${namespace}_cache_expires"] = gmdate('D, d M Y H:i:s', 
        mktime(0, 0, 0, date('m') + 1, 0, date('y'))
    ) . ' GMT';
    $rs["${namespace}_current_year"] = date('Y');
}

/**
 * @link    http://us.php.net/manual/en/function.get-browser.php#86995
 */
function _get_browser ($engine = false) {
    $SUPERCLASS_NAMES = "gecko,mozilla,mosaic,webkit";
    $SUPERCLASS_REGX  = "(?:" . str_replace(",", ")|(?:", $SUPERCLASS_NAMES) . ")";
    $SUBCLASS_NAMES   = "opera,msie,firefox,chrome,safari";
    $SUBCLASS_REGX    = "(?:" . str_replace(",", ")|(?:", $SUBCLASS_NAMES) . ")";
    $browser      = "unrecognized";
    $majorVersion = "0";
    $minorVersion = "0";
    $fullVersion  = "0.0";
    $platform     = 'unrecognized';
    $userAgent    = strtolower($_SERVER['HTTP_USER_AGENT']);
    if ( ! $engine) {
        $found = preg_match("/(?P<browser>" . $SUBCLASS_REGX . ")(?:\D*)(?P<majorVersion>\d*)(?P<minorVersion>(?:\.\d*)*)/i", 
            $userAgent, $matches);
    } elseif ($engine OR ! $found) {
        $found = preg_match("/(?P<browser>" . $SUPERCLASS_REGX . ")(?:\D*)(?P<majorVersion>\d*)(?P<minorVersion>(?:\.\d*)*)/i", 
            $userAgent, $matches);
    }
    if ($found) {
        $browser      = $matches["browser"];
        $majorVersion = $matches["majorVersion"];
        $minorVersion = $matches["minorVersion"];
        $fullVersion  = $matches["majorVersion"] . $matches["minorVersion"];
        if ($browser === "safari") {
            if (preg_match("/version\/(?P<majorVersion>\d*)(?P<minorVersion>(?:\.\d*)*)/i", $userAgent, $matches)) {
                $majorVersion = $matches["majorVersion"];
                $minorVersion = $matches["minorVersion"];
                $fullVersion  = "$majorVersion.$minorVersion";
            }
        }
    }
    if (strpos($userAgent, 'linux')) {
        $platform = 'linux';
    } elseif (strpos($userAgent, 'macintosh') OR strpos($userAgent, 'mac platform x')) {
        $platform = 'mac';
    } elseif (strpos($userAgent, 'windows') OR strpos($userAgent, 'win32')) {
        $platform = 'windows';
    }
    return array("browser"   => $browser,
        "majorVersion" => $majorVersion,
        "minorVersion" => $minorVersion,
        "fullVersion"  => $fullVersion,
        "platform"     => $platform,
        "userAgent"    => $userAgent
    );
}

function truncate ($str, $delim = '. ', $html = true, $suffix = '', $length = 150, $start = 0) 
{
    if ($html) { // remove cdata and html
        $str = preg_replace('/[\n\t\[\]\{\}\/\/<>]*/i', '', strip_tags($str));
    }
    if ($delim === '. ') { // flag fake sentences, abbreviations, etc.
        $str = preg_replace('/(\b[A-Z][A-Za-z0-9]+)(\.\s)/', '$1%period%', $str);
    } else if ($delim === ' ' AND strlen($str) < $length) { // nothing to trim
        return $str;
    }
    // try
    $substr = substr($str, $start, $length);
    $pos = strrpos($substr, $delim);
    // then
    if ($pos === false) { // nothing to trim
        $pos = strpos($str, $delim, $start);
        $substr = substr($str, $start, $pos + 1);
        if (strlen($substr) === 1) { // string is one unit
            $substr = $str;
        }
    } else {
        $substr = substr($substr, 0, $pos + 1);
    }
    if ($delim === '. ') { // revert fake sentences
        $substr = str_replace('%period%', '. ', $substr);
    }
    return rtrim($substr) 
            . ((!empty($suffix) && $substr === $str) ? '' : ' ' . $suffix);
}

// html source prettifier

function clean_newlines ($output)
{
    $pieces = explode("\n", $output);
    foreach ($pieces as $key => $str)
    {
        //Makes sure empty lines are ignores
        if ( ! preg_match("/^(\s)*$/", $str)) {
            $pieces[$key] = preg_replace("/>(\s|\t)*</U", ">\n<", $str);
        }
    }
    return implode("\n", $pieces);
}

function clean_html ($output)
{
    //Set wanted indentation
    $indent = str_repeat(" ", 4);
    //Uses previous function to seperate tags
    $output = clean_newlines($output);
    $output = explode("\n", $output);
    //Sets no indentation
    $indent_level = 0;
    foreach ($output as $key => $value)
    {
        //Removes all indentation
        $value = preg_replace("/\t+/", "", $value);
        $value = preg_replace("/^\s+/", "", $value);
        $indent_replace = "";
        //Sets the indentation from current indent level
        for ($o = 0; $o < $indent_level; $o++)
        {
            $indent_replace .= $indent;
        }
        if (preg_match("/<(.+)\/>/", $value)) { // If self-closing tag, simply apply indent
            $output[$key] = $indent_replace . $value;
        } elseif (preg_match("/<!(.*)>/", $value)) { // If doctype declaration, simply apply indent
            $output[$key] = $indent_replace . $value;
        } elseif (preg_match("/<[^\/](.*)>/", $value) AND preg_match("/<\/(.*)>/", $value)) { 
            // If opening AND closing tag on same line, simply apply indent
            $output[$key] = $indent_replace . $value;
        } elseif (preg_match("/<\/(.*)>/", $value) OR preg_match("/^(\s|\t)*\}{1}(\s|\t)*$/", $value)) {
            // If closing HTML tag or closing JavaScript clams, decrease indentation and then apply the new level
            $indent_level--;
            $indent_replace = "";
            for ($o = 0; $o < $indent_level; $o++)
            {
                $indent_replace .= $indent;
            }
            $output[$key] = $indent_replace . $value;
        } elseif ((preg_match("/<[^\/](.*)>/", $value) AND ! preg_match("/<(link|meta|base|br|img|hr)(.*)>/", $value)) 
            OR preg_match("/^(\s|\t)*\{{1}(\s|\t)*$/", $value)) {
            // If opening HTML tag AND not a stand-alone tag, or opening JavaScript clams, increase indentation and then apply new level
            $output[$key] = $indent_replace . $value;
            $indent_level++;
            $indent_replace = "";
            for ($o = 0; $o < $indent_level; $o++)
            {
                $indent_replace .= $indent;
            }
        } else { // Else only apply indentation
            $output[$key] = $indent_replace . $value;
        }
    }
    // Return single string seperated by newline
    return implode("\n", $output);
}