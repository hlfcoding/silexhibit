<?php if (!defined('SERVICE')) exit('No direct script access allowed');

/**
 * API functionality is serverside and not client side because 
 * of this, we can cache results.
 * @package Indexhibit++
 * @author Peng Wang <peng@pengxwang.com>
 */
interface IStaticCaching 
{    
    const DEFAULT_CACHE_PATH = 'cache/';
    const DEFAULT_CACHE_TIMEOUT = 3600;
    
    function generateCacheFileName ($salt);
    function establishCachePath ($cachePath);
    
    /**
     * @return bool|string
     */
    function readCache ();
    function updateCache ($data);
}

class ApiRequest implements IStaticCaching 
{    
    //---------------------------------------
    // PUBLIC VARIABLES
    //---------------------------------------
    public $serviceAddress;
    public $parameters;
    public $cachePath;
    public $cacheTimeout;
    public $returnJson;
    //---------------------------------------
    // PROTECTED VARIABLES
    //---------------------------------------
    protected $cacheFileName;
    protected $query;
    //---------------------------------------
    // PUBLIC METHODS
    //---------------------------------------
    public function __construct ($serviceAddress, 
                                 $defaultParameters, 
                                 $cacheParameters = array('path' => null, 'timeout' => null),
                                 $returnJson = true) 
                                 // something with json_encode makes it drop elements like `media:image`
    {
        $this->serviceAddress = $serviceAddress;
        $this->parameters = $defaultParameters;
        $this->getCustomParameters();
        $this->query = $this->buildQuery();
        $this->generateCacheFileName($this->query);
        $this->establishCachePath(isset($cacheParameters['path']) ? $cacheParameters['path'] : self::DEFAULT_CACHE_PATH);
        $this->cacheTimeout = isset($cacheParameters['timeout']) ? $cacheParameters['timeout'] : self::DEFAULT_CACHE_TIMEOUT;
        $this->returnJson = $returnJson;
    }
    public function buildQuery () 
    {
        return $this->serviceAddress . '?' . http_build_query($this->parameters, '', '&'); 
    }
    public function fetchResult () 
    {
        if (!$result = $this->readCache()) {
            if ($this->returnJson) {
                $result = json_encode(simplexml_load_file($this->query));
            } else { // xml string
                $result = file_get_contents($this->query);
            }
            try {
                $this->updateCache($result);
            } catch (RuntimeException $e) {
                return $e->getMessage();
            }
        } 
        return $result;
    }
    //---------------------------------------
    // PROTECTED METHODS
    //---------------------------------------
    protected function getCustomParameters () 
    {
        foreach (array_merge($_GET, $_POST) as $name => $value) {
            $value = trim($value);
            if (!empty($name)) {
                $this->parameters[$name] = $value;
                if (!isset($this->parameters[$name])) {
                    unset($this->parameters[$name]);
                }
            }
        }
    }
    //---------------------------------------
    // STATIC CACHING
    //---------------------------------------
    public function generateCacheFileName ($salt) 
    {
        $this->cacheFileName = md5($salt);
    }
    public function establishCachePath ($cachePath) 
    {
        if (!is_dir($cachePath) && !mkdir($cachePath, 0775, true)) {
            throw new RuntimeException('Cannot create cache directory');
        }
        $this->cachePath = $cachePath;
    }
    public function readCache () 
    {
        $cacheFile = $this->cachePath . $this->cacheFileName;
        if (file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $this->cacheTimeout))) {
            $cacheFileHandle = fopen($cacheFile, 'r');
            return fread($cacheFileHandle, filesize($cacheFile));
        }
        return false;
    }
    public function updateCache ($data) 
    {
        $cacheFile = $this->cachePath . $this->cacheFileName;
        if (!$cacheFileHandle = fopen($cacheFile, 'w')) { 
            throw new RuntimeException('Cannot create cache file');
        } else if (fwrite($cacheFileHandle, $data) === false) { // successfully created, but
            throw new RuntimeException('Cannot write to cache file');
        } // otherwise updated successfully
    }
}