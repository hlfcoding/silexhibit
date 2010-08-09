<?php 
/*
    Stand-alone script that interacts with Posterous API. It is called with an  
    XHR and does not reside in the Indexhibit context.
*/
// process XHR
import_request_variables('gp');
$callUrl = 'http://posterous.com/api/readposts';
$callFields = array(
    'num_posts' => 10,
    'page' => 1
    // hostname
    // tag
); // defaults
foreach (get_defined_vars() as $fieldName => $fieldValue)
{
    $fieldValue = trim($fieldValue);
    if (isset($fieldName) && !empty($fieldValue)) {
        $callFields[$fieldName] = $fieldValue;
    }
}
$callQuery = $callUrl . '?' . http_build_query($callFields);
// check cache
define('CACHE_PATH', 'cache/');
define('CACHE_TIMEOUT', 3600); // seconds
if (!is_dir(CACHE_PATH) && !mkdir(CACHE_PATH, 0775, true)) {
    throw new RuntimeException('Cannot create cache directory');
}
$cacheFileName = md5($callQuery);
$cacheFile = CACHE_PATH . $cacheFileName;
if (file_exists($cacheFile) && (filemtime($cacheFile) > (time() - CACHE_TIMEOUT))) {
    $cacheFileHandle = fopen($cacheFile, 'r');
    $resultPosts = fread($cacheFileHandle, filesize($cacheFile));
} else {
    // hit the api
    $resultPosts = json_encode(simplexml_load_file($callQuery));
    // update cache
    if (!$cacheFileHandle = fopen($cacheFile, 'w')) { 
        throw new RuntimeException('Cannot create cache file');
    } else if (fwrite($cacheFileHandle, $resultPosts) === false) { // successfully created, but
        throw new RuntimeException('Cannot write to cache file');
    } // otherwise updated successfully
}
// serve
echo $resultPosts;