<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Frontend helpers / template functions
 * @author Peng Wang <peng@pengxwang.com>
 * @see /ndxz-studio/helper/custom.php
 */

// procedure
load_xml();
add_globals('pxw');

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
// FEED
//---------------------------------------
function include_feed_js () {
    $output = '';
    if (!defined('INCLUDED_FEEDJS')) {
        $path = THEMEURL
            . ((MODE === DEVELOPMENT) ? '/feed.js' : '/' . PRODUCTION .'/feed.min.js');
        $output = "<script type=\"text/javascript\" src=\"$path\"></script>";
        define('INCLUDED_FEEDJS', true);
    }
    return $output;
}
/**
 * Bridge to the Posterous API
 * @param int $section_id 
 * @param string $hostname 
 * @return string
 */
function posterous_feed ($section_id, $hostname) 
{
    global $rs;
    if ($section_id != $rs['data_feed_section_id']) {
        return;
    }
    $output = '';
    $output .= "<script type=\"text/javascript\">
        Site = jQuery.extend(true, Site || {}, {
           feedApiData: {
               posterous: {
                   \"hostname\": '$hostname',
                   \"num_posts\": ${rs['data_posterous_num_posts']} 
               }
           } 
        });
    </script>";
    $output .= include_feed_js();
    return $output;
}
/**
 * Bridge to the Twitter API
 * @param int $section_id 
 * @param string $screen_name 
 * @return string
 */
function twitter_feed ($section_id, $screen_name) 
{
    global $rs;
    if ($section_id != $rs['data_feed_section_id']) {
        return;
    }
    $output = '';
    $output .= "<script type=\"text/javascript\">
        Site = jQuery.extend(true, Site || {}, {
           feedApiData: {
               twitter: {
                   \"screen_name\": '$screen_name',
                   \"count\": ${rs['data_twitter_count']} 
               }
           } 
        });
    </script>";
    $output .= include_feed_js();
    return $output;
}
/**
 * Includes correct template blueprint
 * @param int $section_id 
 * @param string $template_name 
 * @return string
 */
function front_template ($section_id, $template_name) {
    global $rs;
    $output = '';
    switch ("$section_id : $template_name") {
        // 'news feed'
        case "{$rs['data_feed_section_id']} : feed" : 
            $output = file_get_contents(THEMEDIR . '/feed.html');
            break;
    }
    return $output;
}
//---------------------------------------
// UTILITY
//---------------------------------------
function run_antispambot ($field) { return antispambot($field); }
function php_date ($format) { return date($format); }