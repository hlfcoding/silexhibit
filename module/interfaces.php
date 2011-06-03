<?php if (!defined('SITE')) exit('No direct script access allowed');

interface ICMSController 
{
    /**
     * javascript files are not necessarily views in what they do
     * but from the standpoint of a php mvc app, that's exactly what they are
     */
    const DEFAULT_VIEW_BIN_PATH = 'views';
}

interface ICMSPageController extends ICMSController 
{
    function load_pjs ($name, $vars = array());
    function load_phtml ($name, $vars = array());
    function deserialize_html ($html);
    function serialize_html ($html);
    function load_master_js ();
}

interface ICMSAjaxController extends ICMSController 
{
    
}