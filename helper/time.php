<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Time functions
 * @author edited by Peng Wang <peng@pengxwang.com>
 * @version 1.1
 * @package Indexhibit
 **/

/**
 * @global object
 * @param bool
 * @return string
 **/
function getNow($now = true)
{
    $OBJ =& get_instance();
    return ($now === TRUE) ?
        date("Y-m-d H:i:s", time()) :
        date("Y-m-d", time());
}

/**
 * @param string
 * @return string
 **/
function convertToStamp($timestamp)
{
    return date('YmdHis', strtotime($timestamp));
}

/**
 * @param string
 * @param string
 * @param string
 * @todo messy
 * @return string
 **/
function convertDate($date = '', $offset = '', $format = '')
{
    $date = ($date === '') ? getNow() : $date;
    $offset = ($offset === '') ? 0 : $offset;
    $format = ($format === '') ? '%d %B %Y' : $format;
    $timestamp = str_replace(array('-', ':', ' '), array('', '', ''), $date);
    $time[0] = substr($timestamp, 8, 2); // hours
    $time[1] = substr($timestamp, 10, 2); // min
    $time[2] = substr($timestamp, 12, 2); // seconds
    $time[3] = substr($timestamp, 6, 2); // day
    $time[4] = substr($timestamp, 4, 2); // month
    $time[5] = substr($timestamp, 0, 4); // year
    // we need to adjust for the time offset
    $new = date('Y-m-d H:i:s', mktime($time[0]+$offset, $time[1], $time[2], $time[4], $time[3], $time[5]));
    return strftime($format, strtotime($new));
}
