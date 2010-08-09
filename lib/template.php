<?php if (!defined('SITE')) exit('No direct script access allowed');


/**
* indexhibit template class
*
* Still needs more planning - messy
* 
* @version 1.0
* @author Vaska 
*/
class Template
{
    var $title;
    var $body;
    var $index;
    var $css            = array();
    var $js             = array();
    var $ex_js          = array();
    var $data           = array();
    var $location;
    var $location_override;
    var $sub_location   = array();
    var $toggler        = array();
    var $add_script;
    var $action;
    var $action_error;
    var $action_update;
    var $form_type      = FALSE;
    var $form_onsubmit  = FALSE;
    var $notifier       = array();
    var $special_js;
    
    // for popups
    var $pop_location;
    var $pop_links      = array();
    var $pref_nav;
        
    /**
    * Returns basic stuff
    *
    * @param void
    * @return null
    */
    function Template()
    {
        // default settings
        $this->title = 'indexhibit';
        $this->add_css('style.css');
        $this->add_js('common.js');
        $this->cache_expires = gmdate('D, d M Y H:i:s', time() + $expires) . 'GMT';
    }
    
    
    /**
    * Returns string - our basic instrument for outputting pages
    *
    * @param string $tpl
    * @return string
    */
    function tpl_test($tpl)
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
    * Returns update notification string
    *
    * @param void
    * @return string
    */
    function tpl_notify()
    {
        if ($this->notifier == '') return;
        
        $out = "\n";
        
        foreach ($this->notifier as $notify)
        {
            $out .= p($notify);
        }
        
        return div($out, "style='margin-bottom: 18px; border: 1px solid #c00;'");
    }
    
    
    /**
    * Returns doctype
    * (in the future we'll need to account for more options)
    *
    * @param void
    * @return string
    */
    function tpl_type()
    {
        return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
    }

    
    /**
    * Returns array of css files
    *
    * @param string $css
    * @return array
    */
    function add_css($css)
    {
        if (!isset($this->css[$css])) $this->css[$css] = $css;
    }
    
    
    /**
    * Returns array of css files
    *
    * @param string $css
    * @return array
    */
    function del_css($css)
    {
        if (isset($this->css[$css])) unset($this->css[$css]);
    }
    
    
    /**
    * Returns css includes
    *
    * @param void
    * @return string
    */
    function tpl_css()
    {
        if ($this->css == '') return;
        
        $out = '';
        
        foreach ($this->css as $css)
        {
            $out .= "<link type='text/css' rel='stylesheet' href='" . CSS . "$css'/>\n";
        }
        
        return $out;
    }
    
    
    /**
    * Returns array of js files
    *
    * @param string $js
    * @return array
    */
    function add_js($js)
    {
        if (!isset($this->js[$js])) $this->js[$js] = $js;
    }
    
    
    /**
    * Returns array of js files
    *
    * @param string $js
    * @return array
    */
    function add_extended_js($js)
    {
        if (!isset($this->ex_js[$js])) $this->ex_js[$js] = $js;
    }
    
    
    /**
    * Returns array of js files
    *
    * @param string $js
    * @return array
    */
    function del_js($js)
    {
        if (isset($this->js[$js])) unset($this->js[$js]);
    }
    
    
    /**
    * Returns js includes
    *
    * @param void
    * @return string
    */
    function tpl_js()
    {
        if (($this->js == '') && ($this->ex_js == '')) return;
        
        $out = '';
        $out .= "\n";

        if ($this->js != '')
        {
            foreach ($this->js as $js)
            {
                $out .= "<script type='text/javascript' src='" . JS . "$js'></script>\n";
            }
        }
        
        
        if ($this->ex_js != '')
        {
            foreach ($this->ex_js as $js)
            {
                $out .= "<script type='text/javascript' src='$js'></script>\n";
            }
        }
        
        return $out;
    }
    
    
    /**
    * Returns string template output
    *
    * @param string $template
    * @return string
    */
    function output($template)
    {
        return $this->tpl_test($template);
    }
    
    
    /**
    * Returns string template output
    *
    * @param string $template
    * @return string
    */
    function popup($template)
    {
        return $this->tpl_test($template);
    }

    
    /**
    * Returns preferences to page
    *
    * @param void
    * @return string
    */
    function tpl_prefs()
    {
        $out = '';
        
        if (is_array($this->tpl_prefs_nav()))
        {
            foreach ($this->pref_nav as $pref)
            {
                $attr = (!isset($pref['attr'])) ? null : $pref['attr'];
                $out .= ' ' . href($pref['pref'], $pref['link'], $attr);
            }
        }
        
        return $out;
    }
    
    
    /**
    * Returns top navigation
    *
    * @param void
    * @return string
    */
    function tpl_site_menu()
    {
        global $go;
        
        $OBJ =& get_instance();
        $out = '';
        
        if (!is_array($this->tpl_modules())) show_error('no menu created');
        
        $nav = $this->tpl_modules();
        
        $out .= "<ul id='nav'>\n";
        
        foreach ($nav as $key => $doit)
        {
            $active = ($go['a'] == $doit) ? TRUE : FALSE;
            $onoff = ($active == TRUE) ? "class='on'" : "class='off'";
            $out .= li(href(ucwords($OBJ->lang->word($doit)), "?a=$doit"), $onoff);
        }
        
        $out .= "</ul>\n";
        
        return $out;
    }
    

    /**
    * Returns array of installed modules
    *
    * @param void
    * @return array
    */
    function tpl_modules()
    {
        // let's get the folders and info...
        $modules = array();
        $path = DIRNAME . BASENAME . '/module/';

        if (is_dir($path))
        {
            if ($fp = opendir($path)) 
            {
                while (($module = readdir($fp)) !== false) 
                {
                    if ((!eregi("^_",$module)) && (!eregi("^CVS$",$module)) && (!eregi(".php$",$module)) && (!eregi(".html$",$module)) && (!eregi(".DS_Store",$module)) && (!eregi("\.",$module)) && (!eregi("system",$module)))
                    {      
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
    *
    * @param void
    * @return array
    */
    function tpl_prefs_nav()
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
    * Returns string
    *
    * @param void
    * @return string
    */
    function tpl_indexhibit()
    {
        $OBJ =& get_instance();
        return $OBJ->lang->word('indexhibit');
    }
    
    
    /**
    * Return string
    *
    * @param void
    * @return string
    */
    function tpl_foot_left()
    {
        return "&copy; 2008"; 
    }
    
    
    /**
    * Return string
    *
    * @param void
    * @return string
    */
    function tpl_foot_right()
    {
        return "<a href='http://www.indexhibit.org/'>Indexhibit<small><sup>TM</sup></small> v" . VERSION . "</a>";
    }
    
    
    /**
    * Return string
    *
    * @param void
    * @return string
    */
    function tpl_location()
    {
        global $go;
        $OBJ =& get_instance();
        
        $addition = (isset($this->location)) ? ": $this->location": '';
        
        $location = ($this->location_override == '') ? $OBJ->lang->word($go['a']) : $this->location_override;

        return $location . $addition;
    }
    
    
    /**
    * Return string
    *
    * @param void
    * @return string
    */
    function tpl_action()
    {
        $OBJ =& get_instance();
        
        //$color = ($this->action_error != '') ? 'action-error' : 'action';
        
        if ($this->action_update != '')
        {
            return " <span class='action'>" . $OBJ->lang->word($this->action_update) . "</span>";
        }
        
        if ($this->action_error != '')
        {
            return " <span class='action-error'>" . $OBJ->lang->word($this->action_error) . "</span>";
        }
        
        return '';

    }
    
    
    /**
    * Return string
    *
    * @param void
    * @return string
    */
    function tpl_sub_location()
    {
        if ($this->sub_location == '') return;
        
        $OBJ =& get_instance();
        
        $out = '';
        
        foreach ($this->sub_location as $sub)
        {
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
    */
    function tpl_form_type()
    {
        return ($this->form_type == TRUE) ? " enctype='multipart/form-data'" : '';
    }
    
    
    /**
    * Return string
    *
    * @param void
    * @return string
    */
    function tpl_form_onsubmit()
    {
        return ($this->form_type != FALSE) ? " onsubmit=\"$this->form_onsubmit\"" : '';
    }
    
    
    /**
    * Return array
    *
    * @param void
    * @return array
    */
    function tpl_add_script()
    {
        return $this->add_script;
    }

    
    /**
    * Return string
    *
    * @param void
    * @return string
    */
    function tpl_toggler()
    {
        if ($this->toggler == '') return;
        
        $OBJ =& get_instance();
        
        $out = '';
        
        foreach ($this->toggler as $key => $tab)
        {
            $attr = (!isset($tab[2])) ? 'left' : 'right';
            $float = ($attr == 'right') ? "float:right;" : "float:left;";
            $show = ($key == 0) ? " class='tabOn'" : " class='tabOff'";

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
    */
    function tpl_paginate($row, $lim, $query, $string='')
    {
        $OBJ =& get_instance();
        global $go;
        
        // not happy with this...
        $rs = $OBJ->db->fetchArray($query);
        $num = count($rs);
        

        $var = $row - $lim;
            
        if (($row != 0) && (($row - $lim) >= 0) && ($row != ""))
        {
            $back = href('&laquo; '.$OBJ->lang->word('previous'), $string."&amp;page=$var");
        } 
        else 
        { 
            $back = "&nbsp;";   
        }


        if (($row + $lim) < $num) 
        { 
            $var = $row + $lim;
            
            $next = href(" ".$OBJ->lang->word('next')." &raquo;", $string."&amp;page=$var");    
        } 
        else 
        {
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
    */
    function tpl_pop_links()
    {
        if ($this->pop_links == '') return;
        
        $OBJ =& get_instance();
        
        $out = '';
        
        foreach ($this->pop_links as $sub)
        {
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
    */
    function get_special_js()
    {
        if ($this->special_js != '') 
        {
            return $this->special_js;
        }
        else
        {
            return '';
        }
    }

}
