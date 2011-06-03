<?php 
/*
    Stand-alone script that interacts with Posterous API. It is called with an  
    XHR and does not reside in the Indexhibit context.
*/
// process XHR
// error_reporting(E_ALL);
define('SERVICE', 9999);
require 'feed.common.php';

$posterous = new ApiRequest(
    'http://posterous.com/api/readposts',
    array('num_posts' => 10, 'page' => 1, 'site_id' => null, 'hostname' => null, 'tag' => null), 
    array('path' => null, 'timeout' => 3600 * 24) // cache
);

// echo $posterous->buildQuery();
echo $posterous->fetchResult();