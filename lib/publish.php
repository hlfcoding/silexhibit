<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Publish class
 * It actually just validates the title for publishing
 * @version 1.1
 * @package Indexhibit
 * @author Vaska 
 * @author Peng Wang <peng@pengxwang.com>
 **/

class Publish
{
    public $title      = null;
    public $section    = null;

    /**
     * @param string
     * @return string
     **/
    public function urlStrip ($url)
    {
        $search = '/\/+/';
        $replace = '/';
        return preg_replace($search, $replace, $url);
    }

    /**
     * @return string
     * @todo we should make sure we don't end with - and no --'s
     **/
    public function makeTitle ()
    {
        $this->title = explode(" ", $this->title);
        $this->title = implode("-", $this->title);
        return $this->title;
    }

    /**
     * @param void
     * @return string romanized
     * @todo rewrite regex
     **/
    public function cleanTitle ()
    {
        $this->title = utf8Deaccent($this->title, 0);
        $this->title = utf8Romanize($this->title);
        $this->title = preg_replace('/[^a-z0-9- ]/i', '', $this->title);
        return $this->title;
    }

    /**
     * @return string
     **/
    public function processTitle ()
    {
        $this->title = $this->cleanTitle($this->title);
        return strtolower($this->makeTitle($this->title));
    }

    /**
     * @return string
     **/
    public function makeURL ()
    {
        $this->title = $this->processTitle($this->title);
        return $this->urlStrip('/' . $this->section . '/' . $this->title . '/');
    }
}

