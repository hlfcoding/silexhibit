<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Organize class
 * Manages now exhibits are visually organized
 * 1 = chronological (default)
 * 2 = sectional
 * @version 1.1
 * @package Indexhibit
 * @subpackage Indexhibit CMS
 * @author Vaska 
 * @author Peng Wang <peng@pengxwang.com>
 * @todo remove views
 **/
 
class Organize 
{
    public $settings       = array();
    public $prefs          = array();
    public $obj_org;
    
    /**
     * @return string
     **/
    public function order ()
    {
        $OBJ =& get_instance();
        if ($this->obj_org == 1) {
            return $this->chronological();
        } else {
            return $this->sectional();
        }
    }
    
    /**
     * @todo we're taking out the checkboxes not sure it's worth it
     * @todo simplify
     * @return string
     **/
    public function chronological ()
    {
        $OBJ =& get_instance();
        global $go, $default;
        $body = '';
        // get sections
        $sections = $OBJ->db->fetchArray("SELECT * 
            FROM " . PX . "sections 
            ORDER BY sec_ord ASC");
        // pages
        $i = 1;
        foreach ($sections as $key => $section) {
            $rs = $OBJ->db->fetchArray("SELECT * 
                FROM " . PX . "objects  
                WHERE object = '" . OBJECT . "'  
                AND section_id = $section[secid] 
                ORDER BY ord ASC");
            if (!$rs) {
                //if ($OBJ->access->prefs['user_mode'] == 1) {
                    //$input = input('boxy','checkbox',"$checked id='b$section[secid]'",1);
                //} else {
                    //$input = '&nbsp;';
                //}
                $sect = span($section['sec_desc'], "class='inplace1' id='s$section[secid]'");
                //$checked = ($section['sec_disp'] == 1) ? "checked='checked'" : '';
                // if advanced
                //if ($OBJ->access->prefs['user_mode'] == 1) {
                    //$body .= ul("\n".li($sect.
                        //span($input,
                        //"class='options switchBox' style='color: #000;'"),
                        //"class='group'"),
                        //"class='sortable' id='sort$section[secid]'");
                //} else {
                    $body .= ul("\n" . li($sect . 
                        span('&nbsp;'),
                        "class='group'"),
                        "class='sortable' id='sort$section[secid]'");
                //}
            } else {
                // rewrite the projects array for the years
                if ($section['sec_proj'] == 1) {
                    foreach ($rs as $rewrite) {
                        $new[$rewrite['year']][] = array(
                            'id'        => $rewrite['id'],
                            'status'    => $rewrite['status'],
                            'year'      => $rewrite['year'],
                            'title'     => $rewrite['title'],
                            'report'    => $rewrite['report'],
                            'hidden'    => $rewrite['hidden']
                        );
                    }
                    krsort($new);
                    $rs = $new;
                }
                // make the list
                $list = '';
                if ($section['sec_proj'] !== 1) {
                    foreach ($rs as $key => $out) {
                        $status = ($out['status'] == 1) ? 'published' : 'draft';
                        $hidden = ($out['hidden'] == 1) ? "<span class='hidden'>" . $OBJ->lang->word('hidden') . "</span> " : '';
                        $list .= li($out['title'] . 
                            span(href($OBJ->lang->word('preview'), "?a=$go[a]&amp;q=prv&amp;id=" . $out['id']) . ' ' . href($OBJ->lang->word('edit'), "?a=$go[a]&amp;q=edit&amp;id=" . $out['id']),
                            "class='options' style='color: #000;'") . $hidden,
                            "class='sortableitem $status' id='item" . $out['id'] . "'");
                    }
                //if ($OBJ->access->prefs['user_mode'] == 1) {
                    //$input = input('boxy','checkbox',"$checked id='b$section[secid]'",1);
                //} else {
                    $input = '&nbsp;';
                //}
                $sect = span($section['sec_desc'], "class='inplace1' id='s$section[secid]'");
                //$checked = ($section['sec_disp'] == 1) ? "checked='checked'" : '';
                $body .= ul("\n".li($sect.
                    span($input,
                        "class='options switchBox' style='color: #000;'"),
                        "class='group'") . $list,
                        "class='sortable' id='sort$section[secid]'");
                } else {
                    foreach ($rs as $key => $first) {
                        $list = '';
                        foreach ($first as $out) {
                            $status = ($out['status'] == 1) ? 'published' : 'draft';
                            $hidden = ($out['hidden'] == 1) ? "<span class='hidden'>" . $OBJ->lang->word('hidden') . "</span> " : '';
                            $report = ($out['report'] == 1) ? "<span class='report'>" . $OBJ->lang->word('report') . "</span> " : '';
                            $list .= li($out['title'] . 
                                span(href($OBJ->lang->word('preview'), "?a=$go[a]&amp;q=prv&amp;id=" . $out['id']) . ' ' . href($OBJ->lang->word('edit'), "?a=$go[a]&amp;q=edit&amp;id=" . $out['id']),
                                "class='options' style='color: #000;'") . $hidden . $report,
                                "class='sortableitem $status' id='item" . $out['id'] . "'");
                        }
                        $sect = span($key);
                        //$checked = ($section['sec_disp'] == 1) ? "checked='checked'" : '';
                        // if advanced
                        if ($OBJ->access->prefs['user_mode'] == 1) {
                            $body .= ul("\n" . li($sect . 
                                span('&nbsp;',
                                    "class='options' style='color: #000;'"),
                                    "class='group'") . $list,
                                    "class='sortable' id='sort$section[secid]-$key'");
                        } else {
                            $body .= ul("\n" . li($sect . 
                                span('&nbsp;',
                                    "class='options' style='color: #000;'"),
                                    "class='group'") . $list,
                                    "class='sortable' id='sort$section[secid]-$key'");
                        }
                    }
                }
            }
            $i++;
        }
        return $body;
    }
    
    /**
     * @return string
     **/
    public function sectional ()
    {
        $OBJ =& get_instance();
        global $go, $default;
        $body = '';
        // get sections
        $sections = $OBJ->db->fetchArray("SELECT * 
            FROM " . PX . "sections 
            ORDER BY sec_ord ASC");
        // pages
        $i = 1;
        foreach ($sections as $key => $section) {
            $rs = $OBJ->db->fetchArray("SELECT * 
                FROM " . PX . "objects  
                WHERE object = '" . OBJECT . "'  
                AND section_id = $section[secid] 
                ORDER BY ord ASC");
            if (!$rs) {
                $sect = span($section['sec_desc'], "class='inplace1' id='s$section[secid]'");
                $checked = ($section['sec_disp'] == 1) ? "checked='checked'" : '';
                $body .= ul("\n" . li($sect . 
                    span('&nbsp;',
                    "class='options switchBox' style='color: #000;'"),
                    "class='group'"),
                    "class='sortable' id='sort$section[secid]'");
            } else {
                // make the list
                $list = '';
                foreach ($rs as $key => $out) {
                    $status = ($out['status'] == 1) ? 'published' : 'draft';
                    $hidden = ($out['hidden'] == 1) ? "<span class='hidden'>" . $OBJ->lang->word('hidden') . "</span> " : '';
                    $list .= li($out['title'] . 
                        span(href($OBJ->lang->word('preview'), "?a=$go[a]&amp;q=prv&amp;id=" . $out['id']) . ' ' . href($OBJ->lang->word('edit'), "?a=$go[a]&amp;q=edit&amp;id=" . $out['id']),
                        "class='options' style='color: #000;'") . $hidden,
                        "class='sortableitem $status' id='item" . $out['id'] . "'");
                }
                $sect = span($section['sec_desc'], "class='inplace1' id='s$section[secid]'");
                $checked = ($section['sec_disp'] == 1) ? "checked='checked'" : '';
                $body .= ul("\n" . li($sect.
                    span('&nbsp;',
                        "class='options switchBox' style='color: #000;'"),
                        "class='group'") . $list,
                        "class='sortable' id='sort$section[secid]'");
            }
            $i++;
        }
        return $body;
    }
}