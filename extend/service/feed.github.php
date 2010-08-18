<?php 
/*
    Stand-alone script that interacts with Github Atom. It is called with an  
    XHR and does not reside in the Indexhibit context.
*/
// process XHR
// error_reporting(E_ALL);
define('SERVICE', 9999);
require 'feed.common.php';
// TODO hard coded
$github = new ApiRequest(
    'http://github.com/hlfcoding.atom',
    array(),
    array('path' => null, 'timeout' => 6 * 3600) // cache
);

// echo $github->buildQuery();
echo $github->fetchResult();