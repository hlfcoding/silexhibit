<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Routing functions
 * @author edited by Peng Wang <peng@pengxwang.com>
 * @version 1.1
 * @package Indexhibit
 **/

/**
 * A small set of validators specifically for dealing with
 * get, post, cookies upon entry to system.
 * Not the same as our 'processor' class
 * @global array
 * @param type optional Description
 * @return array global
 **/
function directions ()
{
    global $default;
    $go['a']    = getURI('a', $default['module'], 'alpha', 15);
    $go['q']    = getURI('q', 'index', 'alpha', 15);
    $go['id']   = getURI('id', 0, 'digit', 5);
    return $GLOBALS['go'] = $go;
}

/**
 * We aren't using all of these...
 * Simple validation, returns a default if it's not right
 * @param mixed
 * @param string
 * @param array
 * @param int
 * @return mixed
 **/
function check_chars ($default, $str='', $arr, $length) 
{
    $password   = "/^[a-zA-Z0-9]+$/"; // login and password
    $digit      = "/^[0-9]+$/"; // numbers only
    $alpha      = "/^[a-z]+$/"; // lwr case letters only (roman chars)
    $alphaall   = "/^[a-z]+$/i"; // upr & lwr letters only (roman chars)
    $alnum      = "/^[a-z0-9]+$/"; // letters and numbers only
    $iso        = "/^[a-z-_]+$/i"; // upr & lwr letters plus _-
    $email      = "/^[a-zA-Z0-9._-@]+$/"; // email chars
    $connect    = "//"; // used at installer
    // temporary
    $none           = "//"; // not in use?
    // not working yet
    $special1   = "/[a-z0-9]+$/"; // for mainurl info - not in use?
    // check string length
    if (strlen($str) <= $length) {
        return (preg_match($$arr,$str)) ? $str : $default;
    }
    return $default;
}

/**
 * Check and get $_GET var or default
 * @param mixed
 * @param mixed
 * @param bool
 * @param int
 * @param bool
 * @return void
 **/
function getURI ($var, $default, $validate, $length, $upper=false)
{
    $uri = (isset($_GET[$var])) 
        ? check_chars($default, $_GET[$var], $validate, $length) 
        : $default;

    return ($upper === false) ? strtolower($uri) : $uri;
}

/**
 * Check and get $_POST var or default
 * @param mixed
 * @param mixed
 * @param bool
 * @param int
 * @return void
 **/
function getPOST ($var, $default, $validate, $length)
{
    return (isset($_POST[$var])) 
        ? check_chars($default, $_POST[$var], $validate, $length) 
        : $default;
}

/**
 * Check and get $_COOKIE var or default
 * @param mixed
 * @param mixed
 * @param bool
 * @param int
 * @return void
 **/
function getCOOKIE ($var, $default, $validate, $length)
{
    return ($var) 
        ? check_chars($default, $var, $validate, $length) 
        : $default;
}
