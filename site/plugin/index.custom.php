<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Frontend helpers / template functions
 * @author Peng Wang <peng@pengxwang.com>
 * @see /ndxz-studio/helper/custom.php
 */

// procedure
//---------------------------------------
// CONSTANTS
//---------------------------------------
define('THEMEDIR', DIRNAME . BASENAME . DS . SITEPATH . DS . $rs['obj_theme']);
define('THEMEURL', BASEURL . BASEURLNAME . '/site/' . $rs['obj_theme']);
define('FULLURL', str_replace('index.php', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
//---------------------------------------
// GLOBAL RESOURCES
//---------------------------------------
require THEMEDIR . '/header.php';
load_xml(
    SITEPATH . DS . $rs['obj_theme'] . DS . $default['content_xml_filename'], 
    $default['content_xml_namespace']
);
add_globals($indx['theme_namespace']);

// functions
//---------------------------------------
// EMAIL
//---------------------------------------
function the_email ($address, $name = '', $title = '', $class = 'email') 
{
    $tag = href(( ! empty($name) ? $name : $address)
        , 'mailto:' . antispambot($address)
        , ( ! empty($title) ? 'title="' . $title . '"' : '') . ( ! empty($class) ? ' class="' . $class . '"' : ''));
    return $tag;
}
//---------------------------------------
// PHP -> JAVASCRIPT
//---------------------------------------
/**
 * @global array results
 * @return string html
 **/
function api_urls ()
{
    global $rs;
    return implode("\n", array(
        '<script type="text/javascript">',
        "var path = Site.mediaUrl = '" . BASEURL . GIMGS . "/';",
        "Site.serviceUrl = '" . BASEURL . BASEURLNAME . "/" . SERVPATH . "';",
        '</script>'
        ));
}
//---------------------------------------
// FEED
//---------------------------------------
define('NEWSFEED', 'nf'); // namespace
/**
 * @return string html
 **/
function include_feed_js () 
{
    $output = '';
    if (!defined('INCLUDED_FEEDJS')) {
        $path = THEMEURL
            . ((MODE === PRODUCTION) ? '/' . PRODUCTION . '/js/feed.min.js' :  '/js/feed.js');
        $output .= "<script type=\"text/javascript\" src=\"$path\"></script>";
        define('INCLUDED_FEEDJS', true);
    }
    return $output;
}
/**
 * News feed composed of individual feeds pulling from various API requests
 * @global array results
 * @global array base settings
 * @param string id of the page to show it
 * @param string joined list of feeds to show
 * @return string html
 * @see load_xml()
 **/
function news_feed ($section_id, $feeds) {
    global $rs, $default;
    $ns = $default['content_xml_namespace'] . '_' . NEWSFEED;
    $the_section_id = $rs["{$ns}_feed_section_id"];
    if ($section_id !== $the_section_id) {
        return ''; // fail silently and don't show anything
    }
    // get and prep from xml
    $settings = array();
    foreach ($rs as $key => $value) {
        if (strpos($key, "{$ns}_") === 0) { // in namespace
            $settings[preg_replace("/^{$ns}_/", '', $key)] = $value;
        }
    }
    $output = '';
    $service_dir = DIRNAME . BASENAME . DS . SERVPATH . DS;
    $output .= '<script type="text/javascript">
        Site = jQuery.extend(true, Site || {}, {
            feedOrder: ["' . implode('","', explode('_', $feeds)) . '"],
            feedApiData: {' . "\n";
    foreach (explode('_', $feeds) as $feed) {
        if (($feed = trim($feed)) && 
            file_exists($service_dir . "feed.$feed.php")) {
            $output .= "'$feed': {\n";
            foreach ($settings as $key => $value) {
                if (strpos($key, "{$feed}_") !== 0) {
                    continue;
                } else {
                    $key = preg_replace("/^{$feed}_/", '', $key);
                }
                if (is_numeric($value) || is_bool($value)) {
                    $output .= "'$key': $value,\n";
                } elseif (is_string($value)) {
                    $output .= "'$key': '$value',\n";
                }
            }
            $output = rtrim($output, ",\n");
            $output .= "},\n";
        }
    }
    $output = rtrim($output, ",\n");
    $output .= "\n}});\n</script>\n";
    $output .= include_feed_js();
    return $output;
}
/**
 * Includes correct template blueprint
 * @global array results
 * @global array base settings
 * @param int $section_id 
 * @param string $template_name 
 * @return string
 */
function front_template ($section_id, $template_name) 
{
    global $rs, $default;
    $output = '';
    $ns = $default['content_xml_namespace'] . '_';
    
    switch ("$section_id : $template_name") {
        // 'news feed'
        case $rs[$ns . NEWSFEED . '_feed_section_id'] . ' : feed' : 
            $output .= file_get_contents(THEMEDIR . '/feed.html');
            break;
    }
    return $output;
}
//---------------------------------------
// UTILITY
//---------------------------------------
function run_antispambot ($field) { return antispambot($field); }
function php_date ($format) { return date($format); }