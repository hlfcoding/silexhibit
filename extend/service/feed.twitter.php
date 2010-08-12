<?php 
/*
    Stand-alone script that interacts with Twitter API. It is called with an  
    XHR and does not reside in the Indexhibit context.
*/
// process XHR
// error_reporting(E_ALL);
define('SERVICE', 9999);
require 'feed.common.php';

$twitter = new ApiRequest(
    'http://api.twitter.com/1/statuses/user_timeline.xml',
    array('user_id' => null, 'screen_name' => null, 'count' => 10, 'page' => 1, 'trim_user' => true, 'include_rts' => false, 'include_entities' => true),
    array('path' => null, 'timeout' => 3600) // cache
);

// echo $twitter->buildQuery();
echo $twitter->fetchResult();