<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Template class
 * Helps us get from url to the correct class and method
 * @version 1.1
 * @package Indexhibit
 * @author Vaska 
 * @author Peng Wang <peng@pengxwang.com>
 * @todo refine
 * @todo change a bunch of methods to phtml
 **/

class Template
{
    public $title;
    public $body;
    public $index;
    public $css            = array();
    public $js             = array();
    public $ex_js          = array();
    public $data           = array();
    public $location;
    public $location_override;
    public $sub_location   = array();
    public $toggler        = array();
    public $add_script;
    public $action;
    public $action_error;
    public $action_update;
    public $form_type      = false;
    public $form_onsubmit  = false;
    public $notifier       = array();
    public $special_js;
    
    // for popups
    public $pop_location;
    public $pop_links      = array();
    public $pref_nav;
    
    public $cache_expires;
    public $js_globals_ns;
    
    const DEFAULT_JS_ROOT_NS = 'org.indexhibit';
    const DEFAULT_JS_GLOBALS_NS = 'globals';
    
    public function __construct ()
    {
        // default settings
        global $default;
        $this->title = 'indexhibit';
        $this->add_css('style.css');
        $this->add_js('common.js');
        $this->cache_expires = gmdate('D, d M Y H:i:s', time() + $default['cache_time']) . 'GMT';
    }
    
    /**
     * Base page template
     * @param string $tpl
     * @return string
     * @todo rename
     **/
    public function tpl_test ($tpl)
    {
        $OBJ =& get_instance();

        header ('Content-type: text/html; charset=utf-8');

        ob_start();
        include_once TPLPATH . $tpl . '.php';
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
        exit;
    }
    
    /**
     * Update notification template
     * @return string
     **/
    public function tpl_notify ()
    {
        if (empty($this->notifier)) {
            return;
        }
        $out = "\n";
        foreach ($this->notifier as $notify) {
            $out .= p($notify);
        }
        return div($out, "style='margin-bottom: 18px; border: 1px solid #c00;'");
    }
    
    /**
     * Returns doctype
     * @param void
     * @return string
     * @todo account for more options
     **/
    public function tpl_type ()
    {
        return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
    }

    /**
     * Add style
     * @param string Key
     **/
    public function add_css ($css)
    {
        if (!isset($this->css[$css])) {
            $this->css[$css] = $css;
        }
    }
    
    /**
     * Delete style
     * @param string Key
     **/
    public function del_css ($css)
    {
        if (isset($this->css[$css])) {
            unset($this->css[$css]);
        }
    }
    
    /**
     * Returns css includes
     * @return string
     **/
    public function tpl_css ()
    {
        $out = "\n";        
        foreach ($this->css as $css) {
            $out .= "<link type='text/css' rel='stylesheet' href='" . CSS . "$css'/>\n";
        }
        return $out;
    }
    
    /**
     * Add script
     * @param string Key
     **/
    public function add_js ($js)
    {
        if (is_array($js) && !empty($js)) {
            foreach ($js as $i) {
                $this->add_js($i);
            }
            return;
        }
        if (!isset($this->js[$js])) {
            $this->js[$js] = ((MODE === DEVELOPMENT) ? 'dev/' : '') . $js;
        }
    }
    
    public function add_js_globals ($vars, $legacy = false, $ns = null) 
    {
        $ns = is_null($ns) ? self::DEFAULT_JS_ROOT_NS . '.' . self::DEFAULT_JS_GLOBALS_NS : $ns;
        $ns = explode('.', $ns);
        $out = array();
        $out[] = '<script type="text/javascript">';
        $out[] = "window.{$ns[0]} = jQuery.extend(true, window.{$ns[0]} || {}, {";
        $out[] = "'" . implode("': { '", $ns) . "': {";
        $out[] = substr(str_replace('"', '\'', json_encode($vars)), 1, -1);
        $out[] = str_repeat('}', count($ns)) . '});';
        if ($legacy) {
            foreach ($vars as $k => $v) {
                $v = (preg_match('/^\d+|true|false$/i', $v) === 0) ? "'$v'" : $v;
                $out[] = "var $k = $v;";
            }
        }
        $out[] = '</script>';
        $this->add_script = implode(PHP_EOL, $out);
    }
    
    /**
    * Add extended script
    * @param string Key
    * @todo whatever that means
    **/
    public function add_extended_js ($js)
    {
        if (!isset($this->ex_js[$js])) {
            $this->ex_js[$js] = $js;
        }
    }
    
    /**
     * Delete script
     * @param string Key
     **/
    public function del_js ($js)
    {
        if (isset($this->js[$js])) {
            unset($this->js[$js]);
        }
    }
    
    /**
     * Returns js includes
     * @return string
     **/
    public function tpl_js ()
    {
        $out = "\n";
        foreach ($this->js as $js) {
            $out .= "<script type='text/javascript' src='" . JS . "$js'></script>\n";
        }
        foreach ($this->ex_js as $js) {
            $out .= "<script type='text/javascript' src='$js'></script>\n";
        }
        return $out;
    }
    
    /**
     * Returns string template output
     * @param string $template
     * @return string
     **/
    public function output ($template)
    {
        return $this->tpl_test($template);
    }
    
    /**
     * Returns string template output
     * @param string $template
     * @return string
     **/
    public function popup ($template)
    {
        return $this->tpl_test($template);
    }
    
    /**
     * Returns preferences to page
     * @param void
     * @return string
     **/
    public function tpl_prefs ()
    {
        $out = '';
        if (is_array($this->tpl_prefs_nav())) {
            foreach ($this->pref_nav as $pref) {
                $attr = (!isset($pref['attr'])) ? null : $pref['attr'];
                $out .= ' ' . href($pref['pref'], $pref['link'], $attr);
            }
        }
        return $out;
    }
    
    /**
     * Top navigation template
     * @return string
     **/
    public function tpl_site_menu ()
    {
        global $go;
        
        $OBJ =& get_instance();
        $out = '';
        
        if (!is_array($this->tpl_modules())) show_error('no menu created');
        
        $nav = $this->tpl_modules();
        
        $out .= "<ul id='nav'>\n";
        
        foreach ($nav as $key => $doit) {
            $active = ($go['a'] === $doit);
            $onoff = ($active === true) ? "class='on'" : "class='off'";
            $out .= li(href(ucwords($OBJ->lang->word($doit)), "?a=$doit"), $onoff);
        }
        
        $out .= "</ul>\n";
        
        return $out;
    }
    
    /**
     * Get the folders and info
     * @return array<string>
     **/
    public function tpl_modules ()
    {
        $modules = array();
        $path = DIRNAME . BASENAME . DS . load_path('mod') . DS;
        if (is_dir($path)) {
            if ($fp = opendir($path)) {
                while (($module = readdir($fp)) !== false) {
                    if (preg_match('/^(_|CVS$)/i', $module) === 0 &&
                        preg_match('/\.(php|html)$/i', $module) === 0 &&
                        preg_match('/\.(|DS_Store|svn|git)$/i', $module) === 0 && 
                        preg_match('/system/i', $module) === 0) {      
                        $modules[] = $module;
                    }
                } 
            }
            closedir($fp);
        }
        sort($modules);
        clearstatcache();
        return $modules;
    }
    
    /**
     * Returns array of preference parts
     * @return array
     **/
    public function tpl_prefs_nav ()
    {
        $OBJ =& get_instance();
        
        $this->pref_nav['view'] = array(
            'pref' => '<strong>' . $OBJ->lang->word('view site') . '</strong>', 'link' => BASEURL . '/', 'attr' => "class='prefs'");
        
        $this->pref_nav['prefs'] = array(
            'pref' => $OBJ->lang->word('preferences'), 'link' => '?a=system', 'attr' => "class='prefs'");
            
        $this->pref_nav['help'] = array(
            'pref' => $OBJ->lang->word('help'), 'link' => 'http://www.indexhibit.org/forum/', 'attr' => "class='prefs'");
            
        $this->pref_nav['logout'] = array(
            'pref' => $OBJ->lang->word('logout'), 'link' => '?a=system&amp;q=logout', 'attr' => "class='prefs'");
            
        return $this->pref_nav;
    }
    
    /**
     * Name template
     * @return string
     **/
    public function tpl_indexhibit ()
    {
        $OBJ =& get_instance();
        return $OBJ->lang->word('indexhibit');
    }
    
    /**
     * Foot template
     * @return string
     **/
    public function tpl_foot_left ()
    {
        return '&copy; ' . date('Y'); 
    }
    
    /**
     * Foot template
     * @return string
     **/
    public function tpl_foot_right ()
    {
        return '<a href="http://www.indexhibit.org/">Indexhibit</a><a href="http://github.com/hlfcoding/hlf-ndxz">++ v' . VERSION . '</a>';
    }
    
    /**
     * Template route
     * @return string
     **/
    public function tpl_location ()
    {
        global $go;
        $OBJ =& get_instance();
        $addition = (isset($this->location)) ? ": $this->location": '';
        $location = (empty($this->location_override)) ? $OBJ->lang->word($go['a']) : $this->location_override;
        return $location . $addition;
    }
    
    /**
     * Action template
     * @return string
     **/
    public function tpl_action ()
    {
        $OBJ =& get_instance();        
        if ($this->action_update !== '') {
            return " <span class='action'>" . $OBJ->lang->word($this->action_update) . "</span>";
        }
        if ($this->action_error !== '') {
            return " <span class='action-error'>" . $OBJ->lang->word($this->action_error) . "</span>";
        }
        return '';
    }
    
    /**
     * Return string
     *
     * @param void
     * @return string
     **/
    public function tpl_sub_location ()
    {
        if (empty($this->sub_location)) {
            return;
        }
        $OBJ =& get_instance();
        $out = '';
        foreach ($this->sub_location as $sub) {
            $attr = (!isset($sub[2])) ? null : $sub[2];
            $out .= ' ' . href($OBJ->lang->word($sub[0]), $sub[1], $attr);
        }
        return $out;
    }

    /**
     * Return string
     *
     * @param void
     * @return string
     **/
    public function tpl_form_type ()
    {
        return ($this->form_type === true) ? " enctype='multipart/form-data'" : '';
    }
    
    /**
     * Return string
     *
     * @param void
     * @return string
     **/
    public function tpl_form_onsubmit ()
    {
        return ($this->form_type !== false) ? " onsubmit=\"$this->form_onsubmit\"" : '';
    }    
    
    /**
     * Add script template call
     * @return string
     **/
    public function tpl_add_script ()
    {
        return $this->add_script;
    }

    /**
     * Toggler template
     * @return string
     **/
    public function tpl_toggler ()
    {
        if (empty($this->toggler)) {
            return;
        }
        $OBJ =& get_instance();
        $out = '';
        foreach ($this->toggler as $key => $tab) {
            $attr = (!isset($tab[2])) ? 'left' : 'right';
            $float = ($attr === 'right') ? "float:right;" : "float:left;";
            $show = ($key === 0) ? " class='tabOn'" : " class='tabOff'";
            $out .= li(href($tab[0],"#"),"id='a$tab[0]' style='$float' onclick=\"editTab('$tab[0]');\"$show");
        }
        return ul($out,"class='tabs'").div('<!-- -->',"class='cl'");
    }
    
    /**
     * Return pagination array
     *
     * @param integer $row
     * @param integer $lim
     * @param string $string
     * @param string $string
     * @return array
     * @todo not happy with fetchArray call
     **/
    function tpl_paginate ($row, $lim, $query, $string='')
    {
        $OBJ =& get_instance();
        global $go;
        $rs = $OBJ->db->fetchArray($query);
        $num = count($rs);
        $var = $row - $lim;
        if (($row !== 0) && (($row - $lim) >= 0) && ($ro !== '')) {
            $back = href('&laquo; ' . $OBJ->lang->word('previous'), $string . "&amp;page=$var");
        } 
        else { 
            $back = "&nbsp;";   
        }
        if (($row + $lim) < $num) { 
            $var = $row + $lim;
            $next = href(' ' . $OBJ->lang->word('next') . " &raquo;", $string . "&amp;page=$var");    
        } else {
            $next = '';
        }
        $s['total'] = $num;
        $s['back']  = $back;
        $s['next']  = $next;
        return $s;
    }
    
    /**
     * Return string
     *
     * @param void
     * @return string
     **/
    function tpl_pop_links ()
    {
        if (empty($this->pop_links)) {
            return; 
        } 
        $OBJ =& get_instance();
        $out = '';
        foreach ($this->pop_links as $sub) {
            $attr = (!isset($sub[2])) ? null : $sub[2];
            $out .= ' ' . href($OBJ->lang->word($sub[0]), $sub[1], $attr);
        }
        return $out;
    }
    
    /**
     * @return string
     **/
    public function get_special_js ()
    {
        return $this->special_js;
    }

}
