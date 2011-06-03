<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Statistics class
* Frontend statistics - partly crufted from Shortstats
* @version 1.1
* @package Indexhibit
* @author Vaska 
* @author Peng Wang <peng@pengxwang.com>
*/
class Statistics
{

    public function __construct ()
    {
        $this->stat_insertHit();
    }

    /**
     * @param string
     * @return boolean
     **/
    public function stat_ignore_hit ($ip = '')
    {
        global $default;
        $ignored_ips = $default['ignore_ip'];
        foreach ($ignored_ips as $ips) {
            if (strpos($ip, $ips, 0) === 0) return true;
        }
        return false;
    }

    /**
     * @param void
     * @return array stats info
     * @todo $stat['ref'] wonky
     **/
    public function stat_doStats ()
    {
        $stat['ip']         = $_SERVER['REMOTE_ADDR'];
        $stat['lang']       = $this->stat_getLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $stat['ref']        = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
        $stat['url']        = parse_url($stat['ref']);
        $stat['domain']     = (isset($stat['url']['host']))
            ? preg_replace('/^www\./i', '', $stat['url']['host'])
            : '';
        $stat['uri']        = $_SERVER['REQUEST_URI'];
        $stat['agent']      = $_SERVER['HTTP_USER_AGENT'];
        $stat['browser']    = $this->stat_getAgent($stat['agent']);
        $stat['keywords']   = $this->stat_getKeywords($stat['url']);
        $stat['ref']        = ($stat['ref'] === null) ? '' : $stat['ref'];
        return $stat;
    }

    /**
     * Avoid our own site from referrer stats
     * @param string
     * @return string
     **/
    public function stat_reduceURL ($input = '')
    {
        if (empty($input)) {
            return null;
        }
        $url = parse_url($input);
        return preg_replace('/^www./', '', $url['host']);
    }


    /**
     * Works if a person has this table data installed
     * @param string
     * @return string country name
     **/
    public function stat_getCountry ($ip = '')
    {
        $OBJ =& get_instance();
        if (empty($ip)) {
            return;
        }
        $ip = sprintf("%u", ip2long($ip));
        $rs = $OBJ->db->fetchRecord("SELECT country_name FROM iptocountry 
            WHERE ip_from <= " . $OBJ->db->escape($ip) . " AND ip_to >= " . $OBJ->db->escape($ip) . "");
        if ($rs) {
            return trim(ucwords(preg_replace('/([A-Z\xC0-\xDF])/e',
                "chr(ord('\\1')+32)", $rs['country_name'])));
        }
        return '';
    }

    /**
     * @param string
     * @return string
     **/
    public function stat_getLanguage ($lang = '')
    {
        return (!preg_match("/([^,;]*)/", $lang, $langs)) ? 'n/a' : $langs[0];
    }

    /**
     * @param string
     * @return string
     **/
    public function stat_getKeywords ($url = '')
    {
        $searchterms = '';
        if (!isset($url['host'])) {
            return '';
        }
        // this should probably be updated
        $searches = array(
            array("/google\./i", 'q'),
            array("/alltheweb\./i", 'q'),
            array("/yahoo\./i", 'p'),
            array("/search\.aol\./i", 'query'),
            array("/search\.msn\./i", 'q')
        );
        foreach ($searches as $search) {
            if (preg_match($search[0], $url['host'])) {
                parse_str($url['query'], $q);
                return $q[$search[1]];
            }   
        }
        return $searchterms;
    }

    /**
     * @param string user agent string
     * @return array
     **/
    public function stat_getAgent ($ua)
    {
        if (function_exists('_get_browser')) {
            $_get_browser_results = _get_browser();
            $browser = array(
                'browser' => $_get_browser_results['browser'],
                'platform' => $_get_browser_results['platform']
            );
            return $browser;
        }
        $browser['platform']    = "Indeterminable";
        $browser['browser']     = "Indeterminable";    
        // test for platform
        $platforms = array('Windows'=>'Win', 'Macintosh'=>'Mac', 'Linux'=>'Linux');
        foreach ($platforms as $key => $test) {       
            if (strpos($ua, $test) !== false) {
                $browser['platform'] = $key;
            }
        }
        // add AppleWebkit
        $browsers = array(
            array('Netscape', 'Mozilla/4', 'Mozilla/([[:digit:]\.]+)'),
            array('Mozilla', 'Mozilla/5', 'rv(:| )([[:digit:]\.]+)'),
            array('Safari', 'Safari', 'Safari/([[:digit:]\.]+)'),
            array('Firefox', 'Firefox', 'Firefox/([[:digit:]\.]+)'),
            array('Netscape', 'Netscape', 'Netscape[0-9]?/([[:digit:]\.]+)'),
            array('Internet Explorer', 'MSIE', 'MSIE ([[:digit:]\.]+)'),
            array('Crawler/Search Engine', 'Crawl', 'stop'),
            array('Crawler/Search Engine', 'bot', 'stop'),
            array('Crawler/Search Engine', 'slurp', 'stop'),
            array('Lynx', 'Lynx', 'Lynx/([[:digit:]\.]+)'),
            array('Links', 'Links', '\(([[:digit:]\.]+)')
            );
        foreach ($browsers as $test) {
            if (strpos($ua, $test[1]) !== false) {
                $browser['browser'] = $test[0];
            }
        }    
        return $browser;
    }

    public function stat_insertHit ()
    {
        $OBJ =& get_instance();
        $stat = $this->stat_doStats();
        // ignore ip's listed in the config file
        if ($this->stat_ignore_hit($stat['ip']) === true) {
            return;
        }
        // it needs to end with a '/' for it to be a stat
        if ((substr($stat['uri'], -1) !== '/')) {
            return;
        }
        // we don't refer to ourselves
        $found = strpos($this->stat_reduceURL($stat['ref']), $this->stat_reduceURL(BASEURL));
        $stat['ref'] = ($found === false) ? $stat['ref'] : '';
        $clean['hit_addr']      = $stat['ip'];
        $clean['hit_country']   = $this->stat_getCountry($stat['ip']);
        $clean['hit_lang']      = $stat['lang'];
        $clean['hit_domain']    = $stat['domain'];
        $clean['hit_referrer']  = $stat['ref'];
        $clean['hit_page']      = $stat['uri'];
        $clean['hit_agent']     = $stat['agent'];
        $clean['hit_keyword']   = $stat['keywords'];
        $clean['hit_os']        = $stat['browser']['platform'];
        $clean['hit_browser']   = $stat['browser']['browser'];
        $clean['hit_time']      = getNow();
        $OBJ->db->insertArray('statistic', $clean);
        return;
    }
}

