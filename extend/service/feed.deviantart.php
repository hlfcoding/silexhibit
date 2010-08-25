<?php 
/*
    Stand-alone script that interacts with DeviantArt RSS API. It is called with an  
    XHR and does not reside in the Indexhibit context.
*/
// process XHR
// error_reporting(E_ALL);
define('SERVICE', 9999);
require 'feed.common.php';
// TODO hard coded
$da = new ApiRequest(
    'http://backend.deviantart.com/rss.xml',
    array('q' => 'gallery:'),
    array('path' => null, 'timeout' => 6 * 3600), // cache
    false
);

// echo $da->buildQuery();
header('Content-Type: text/html; charset=utf-8');
echo $da->fetchResult();