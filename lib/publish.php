<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Publish class
*
* It actually just validates the title for publishing
* 
* @version 1.0
* @author Vaska 
*/
class Publish
{
    var $title      = NULL;
    var $section    = NULL;

    /**
    * Returns string
    *
    * @param string $url
    * @return string
    */
    function urlStrip($url)
    {
        $search = '/\/+/';
        $replace = '/';

        return preg_replace($search, $replace, $url);
    }

    /**
    * Returns string
    *
    * @param void
    * @return string
    */
    function makeTitle()
    {
        $this->title = explode(" ", $this->title);
        $this->title = implode("-", $this->title);
        
        // we should make sure we don't end with - and no --'s
        
        return $this->title;
    }

    /**
    * Returns 'romanized' string
    *
    * @param void
    * @return string
    */
    function cleanTitle()
    {
        $this->title = utf8Deaccent($this->title, 0);
        $this->title = utf8Romanize($this->title);
            
        // need to rewrite this
        $this->title = preg_replace('/[^a-z0-9- ]/i', '', $this->title);
            
        return $this->title;
    }

    /**
    * Returns string
    *
    * @param void
    * @return string
    */
    function processTitle()
    {
        $this->title = $this->cleanTitle($this->title);
        return strtolower($this->makeTitle($this->title));
    }

    /**
    * Returns string
    *
    * @param void
    * @return string
    */
    function makeURL()
    {
        $this->title = $this->processTitle($this->title);
        return $this->urlStrip('/' . $this->section . '/' . $this->title . '/');
    }
}

