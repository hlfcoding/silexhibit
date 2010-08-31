<?php if (!defined('SITE')) exit('No direct script access allowed');

interface ICMSController {
    const DEFAULT_VIEW_BIN_PATH = 'views';
}

interface ICMSPageController extends ICMSController {
    function load_pjs ($name, $vars = array());
    function load_phtml ($name, $vars = array());
}

interface ICMSAjaxController extends ICMSController {
    
}