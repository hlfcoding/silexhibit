<?php if (!defined('SITE')) exit('No direct script access allowed');


class Stats extends Router
{
    var $error      = FALSE;
    var $error_msg;
    
    function Stats()
    {
        parent::Router();
    }
    
    function page_index()
    {
        global $go;
        
        // default/validate $_GET
        $go['page'] = getURI('page', 0, 'digit', 5);

        $this->template->location = $this->lang->word('main');
        
        // sub-locations
        $this->template->sub_location[] = array($this->lang->word('referrers'),
            "?a=$go[a]&amp;q=refer");
        $this->template->sub_location[] = array($this->lang->word('page visits'),
            "?a=$go[a]&amp;q=hits");
        
        load_module_helper('files', $go['a']);      

        $today = convertToStamp(getNow());
        $day = substr($today,6,2);
        $mn = substr($today,4,2);
        $yr = substr($today,0,4);
        $thirtydays = date('Y-m-d', mktime('00', '00', '00', $mn-1, $day, $yr));
        
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++
        
        $body = "<div class='half'>\n";
        $body .= "<div class='cola'>\n";
        
        $total_since_hits = $this->db->getCount("SELECT count(*) FROM ".PX."stats");
        $total_since_unique_hits = $this->db->getCount("SELECT COUNT(DISTINCT hit_addr) AS 'total' FROM ".PX."stats");
        $total_since_refer_hits = $this->db->getCount("SELECT COUNT(DISTINCT hit_addr) AS 'total' FROM ".PX."stats WHERE hit_referrer != ''");
        
        $body .= "<table class='table380' cellpadding='0' cellspacing='0' border='0'>\n";
        $body .= "<tr>\n";
        $body .= th($this->lang->word('since'), "class='toptext' width='40%'");
        $body .= th($this->lang->word('total'), "class='toptext cell-middle' width='20%'");
        $body .= th($this->lang->word('unique'), "class='toptext cell-middle' width='20%'");
        $body .= th($this->lang->word('refers'), "class='toptext cell-middle' width='20%'");
        $body .= "</tr>\n";
        $body .= "<tr class='over'>\n";
        $body .= td(convertDate($this->access->settings['installdate'], 
            $this->access->prefs['user_offset'],
            $this->access->prefs['user_format']),"class='cell-doc'");
        $body .= td('<b>'.$total_since_hits.'</b>',"class='cell-middle'");
        $body .= td('<b>'.$total_since_unique_hits.'</b>',"class='cell-middle'");
        $body .= td('<b>'.$total_since_refer_hits.'</b>',"class='cell-middle'");
        $body .= "</tr>\n";
        $body .= "</table>\n";
        
        // ++++++++++++++++++++++++++++++++++++++++++++
        
        // dailies
        $days = getDailyHits(NULL);
        $body .= "<table class='table380' cellpadding='0' cellspacing='0' border='0'>\n";
        $body .= "<tr>\n";
        $body .= th($this->lang->word('this week'), "class='toptext' width='40%'");
        $body .= th($this->lang->word('total'), "class='toptext cell-middle' width='20%'");
        $body .= th($this->lang->word('unique'), "class='toptext cell-middle' width='20%'");
        $body .= th($this->lang->word('refers'), "class='toptext cell-middle' width='20%'");
        $body .= "</tr>\n";
        
        $i = 1;
        foreach ($days as $key => $out) {
            $body .= "<tr".row_color(" class='color'").">\n";
            $body .= td($this->lang->word($key), "class='cell-doc'");
            $body .= td($this->db->getCount("SELECT count(*) FROM ".PX."stats WHERE hit_time > '$out[0]' AND hit_time < '$out[1]'"),"class='cell-middle'");
            $body .= td($this->db->getCount("SELECT count(DISTINCT hit_addr) FROM ".PX."stats WHERE hit_time > '$out[0]' AND hit_time < '$out[1]'"),"class='cell-middle'");
            $body .= td($this->db->getCount("SELECT count(DISTINCT hit_addr) FROM ".PX."stats WHERE hit_time > '$out[0]' AND hit_time < '$out[1]' AND hit_referrer != ''"),"class='cell-middle'");
            $body .= "</tr>\n";
            $i++;
        }
        $body .= "</table>\n";
        
        // ++++++++++++++++++++++++++++++++++++++++++++
        
        $week = getWeekHits(NULL);
        $body .= "<table class='table380' cellpadding='0' cellspacing='0' border='0'>\n";
        $body .= "<tr>\n";
        $body .= th($this->lang->word('this month'), "class='toptext' width='40%'");
        $body .= th($this->lang->word('total'), "class='toptext cell-middle' width='20%'");
        $body .= th($this->lang->word('unique'), "class='toptext cell-middle' width='20%'");
        $body .= th($this->lang->word('refers'), "class='toptext cell-middle' width='20%'");
        $body .= "</tr>\n";
        
        $i = 1;
        foreach ($week as $key => $out) {
            $body .= "<tr".row_color(" class='color'").">\n";
            $body .= td($this->lang->word($key),"class='cell-doc'");
            $body .= td($this->db->getCount("SELECT count(*) FROM ".PX."stats WHERE hit_time > '$out[0]' AND hit_time < '$out[1]'"),"class='cell-middle'");
            $body .= td($this->db->getCount("SELECT count(DISTINCT hit_addr) FROM ".PX."stats WHERE hit_time > '$out[0]' AND hit_time < '$out[1]'"),"class='cell-middle'");
            $body .= td($this->db->getCount("SELECT count(DISTINCT hit_addr) FROM ".PX."stats WHERE hit_time > '$out[0]' AND hit_time < '$out[1]' AND hit_referrer != ''"),"class='cell-middle'");
            $body .= "</tr>\n";
            $i++;
        }
        $body .= "</table>\n";
        
        // ++++++++++++++++++++++++++++++++++++++++++++
        
        $months = getMonthlyHits(NULL);
        $body .= "<table class='table380' cellpadding='0' cellspacing='0' border='0'>\n";
        $body .= "<tr>\n";
        $body .= th($this->lang->word('year'), "class='toptext' width='40%'");
        $body .= th($this->lang->word('total'), "class='toptext cell-middle' width='20%'");
        $body .= th($this->lang->word('unique'), "class='toptext cell-middle' width='20%'");
        $body .= th($this->lang->word('refers'), "class='toptext cell-middle' width='20%'");
        $body .= "</tr>\n";
        
        $i = 1;
        foreach ($months as $key => $out) {
            
            $numero = $this->db->getCount("SELECT count(*) FROM ".PX."stats WHERE hit_time LIKE '$out[0]%'");

            $body .= "<tr".row_color(" class='color'").">\n";
            $body .= td($this->lang->word($key), "class='cell-doc'");
            $body .= td($numero,"class='cell-middle'");
            $body .= td($this->db->getCount("SELECT count(DISTINCT hit_addr) FROM ".PX."stats WHERE hit_time LIKE '$out[0]%'"),"class='cell-middle'");
            $body .= td($this->db->getCount("SELECT count(DISTINCT hit_addr) FROM ".PX."stats WHERE hit_time LIKE '$out[0]%' AND hit_referrer != ''"),"class='cell-middle'");
            $body .= "</tr>\n";
            $i++;
        }
        $body .= "</table>\n";
        $body .= "</div>\n";
        
        // ++++++++++++++++++++++++++++++++++++++++++++
        
        $body .= "<div class='colb'>\n";
        
        // ++++++++++++++++++++++++++++++++++++++++++++
        
        // top referrers...
        // we need to forget our own host...
        $repeat = $this->db->fetchArray("SELECT hit_referrer,hit_domain, COUNT(hit_referrer) AS 'refer' FROM ".PX."stats WHERE hit_referrer != '' AND hit_domain != '' AND hit_time > '$thirtydays' GROUP by hit_referrer ORDER BY refer DESC LIMIT 10");
        if (is_array($repeat)) {
        $body .= "<table class='table380' cellpadding='0' cellspacing='0' border='0'>\n";
        $body .= "<tr>\n";
        $body .= th($this->lang->word('top 10 referrers').' '.
        span("(".$this->lang->word('past 30').")","class='small-txt'")
        ,"class='toptext' width='75%'");
        $body .= th('Total',"class='toptext cell-middle' width='25%'");
        $body .= "</tr>\n";
        
        $i = 1;
        foreach ($repeat as $out) {
            $body .= "<tr".row_color(" class='color'").">\n";
            $host = parse_url($out['hit_referrer']);
            $body .= td(href($out['hit_domain'],$out['hit_referrer'],"target='_new'"),"class='cell-doc'");
            $body .= td($out['refer'],"class='cell-middle'");
            $body .= "</tr>\n";
            $i++;
        }
        $body .= "</table>\n";
        }
        
        
        // top search terms...
        $terms = $this->db->fetchArray("SELECT hit_keyword, COUNT(hit_keyword) AS 'keywords' FROM ".PX."stats WHERE hit_keyword != '' AND hit_time > '$thirtydays' GROUP by hit_keyword ORDER BY keywords DESC LIMIT 10");
        if (is_array($terms)) {
        $body .= "<table class='table380' cellpadding='0' cellspacing='0' border='0'>\n";
        $body .= "<tr>\n";
        $body .= th($this->lang->word('top 10 keywords').' '.
        span("(".$this->lang->word('past 30').")","class='small-txt'")
        ,"class='toptext' width='75%'");
        $body .= th('Total',"class='toptext cell-middle' width='25%'");
        $body .= "</tr>\n";
        
        $i = 1;
        foreach ($terms as $out) {
            $body .= "<tr".row_color(" class='color'").">\n";
            $keyword = ($out['hit_keyword'] == '') ? 'Unknown' : $out['hit_keyword'];
            $body .= td($keyword,"class='cell-doc'");
            $body .= td($out['keywords'],"class='cell-middle'");
            $body .= "</tr>\n";
            $i++;
        }
        $body .= "</table>\n";
        }
        
        
        // if installed...
        // top countries...
        $cntry = $this->db->fetchArray("SELECT hit_country, COUNT(hit_country) AS 'total' FROM ".PX."stats WHERE hit_country != '' AND hit_time > '$thirtydays' GROUP by hit_country ORDER BY total DESC LIMIT 10");
        if (is_array($cntry)) {
        $body .= "<table class='table380' cellpadding='0' cellspacing='0' border='0'>\n";
        $body .= "<tr>\n";
        $body .= th($this->lang->word('top 10 countries').' '.
        span("(".$this->lang->word('past 30').")","class='small-txt'")
        ,"class='toptext' width='75%'");
        $body .= th('Total',"class='toptext cell-middle' width='25%'");
        $body .= "</tr>\n";
        
        $i = 1;
        
        foreach ($cntry as $out) {
            $body .= "<tr".row_color(" class='color'").">\n";
            $country = ($out['hit_country'] == '') ? 'Unknown' : $out['hit_country'];
            $body .= td($country,"class='cell-doc'");
            $body .= td($out['total'],"class='cell-middle'");
            $body .= "</tr>\n";
            $i++;
        }
        $body .= "</table>\n";
        }
        
        // ++++++++++++++++++++++++++++++++++++++++++++
        
        $body .= "</div>\n";
        
        // ++++++++++++++++++++++++++++++++++++++++++++
        
        $body .= "<div class='cl'><!-- --></div>\n\n";
        $body .= "</div>\n";
        
                
        $this->template->body = $body;
        
        return;
    }
    
    
    function page_refer()
    {
        global $go;
        
        // default/validate $_GET
        $go['page'] = getURI('page', 0, 'digit', 3);

        $this->template->location = $this->lang->word('main');
        
        // sub-locations
        $this->template->sub_location[] = array($this->lang->word('page visits'),
            "?a=$go[a]&amp;q=hits");
        $this->template->sub_location[] = array($this->lang->word('main'),
            "?a=$go[a]");
        
        load_module_helper('files', $go['a']);
        

        $today = convertToStamp(getNow());
        $day = substr($today,6,2);
        $mn = substr($today,4,2);
        $yr = substr($today,0,4);
        $thirtydays = date('Y-m-d', mktime('00', '00', '00', $mn-1, $day, $yr));
        
        $rs = $this->db->fetchArray("SELECT * FROM ".PX."stats 
            WHERE hit_referrer != '' 
            ORDER by hit_time DESC 
            LIMIT $go[page]," . $this->access->prefs['threads']*2 . "");
        
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++
        
        
        // table for all our results
        $body = "<table cellpadding='0' cellspacing='0' border='0'>\n";
        $body .= "<tr class='top'>\n";
        $body .= "<th width='17%' class='toptext'><strong>".$this->lang->word('ip')." | ".$this->lang->word('date')."</strong></th>\n";
        $body .= "<th width='13%' class='toptext'><strong>".$this->lang->word('country')."</strong></th>\n";
        $body .= "<th width='50%' class='toptext'><strong>".$this->lang->word('page')." | ".$this->lang->word('refers')."</strong></th>\n";
        $body .= "<th width='20%' class='toptext'><strong>".$this->lang->word('keyword')."</strong></th>\n";
        $body .= "</tr>\n";
        $body .= "</table>\n";
        
        // dynamic output for table
        $body .= "<table cellpadding='0' cellspacing='0' border='0'>\n";
        if (!$rs)
        {
            $body .= tr(td('No records yet', "colspan='4'"));
        }
        else
        {
            foreach($rs as $ar) {
            $body .= tr(
                td(href($ar['hit_addr'],
            "http://www.dnsstuff.com/tools/city.ch?ip=$ar[hit_addr]","target='_new'").
                    "<br /><small>" . convertDate($ar['hit_time'], 
                        $this->access->prefs['user_offset'], 
                        $this->access->prefs['user_format'] . ' %T') . "</small>",
                    "width='17%' class='cell-doc'").
                td($ar['hit_country'],"width='13%' class='cell-doc'").
                td($ar['hit_page'].br().
                    href($ar['hit_domain'], $ar['hit_referrer']),
                    "width='50%' class='cell-mid'").
                td($ar['hit_keyword'],"width='20%' class='cell-mid'"),
                    row_color(" class='color'"));
            }
        }
        // end dynamic rows output
        $body .= "</table>\n";
        
        // pagination
        $paginate = $this->template->tpl_paginate($go['page'], $this->access->prefs['threads']*2,
            "SELECT hit_id FROM ".PX."stats WHERE hit_referrer != ''",
            "?a=$go[a]&q=refer");

        $num = ($paginate['total'] == 0) ? '&nbsp;': $paginate['total'].' '.$this->lang->word('total pages');
            
        $body .=
            div(div($num,"class='col'").
                div($paginate['back'].$paginate['next'],"class='col txt-right'").
                div("<!-- -->","class='cl'"),"class='c2 brdr-top'");
        
        
        $this->template->body = $body;
        
        return;
    }
    
    
    function page_hits()
    {
        global $go;
        
        // default/validate $_GET
        $go['page'] = getURI('page', 0, 'digit', 5);

        $this->template->location = $this->lang->word('main');
        
        // sub-locations
        $this->template->sub_location[] = array($this->lang->word('referrers'),
            "?a=$go[a]&amp;q=refer");
        $this->template->sub_location[] = array($this->lang->word('main'),
            "?a=$go[a]");
        
        load_module_helper('files', $go['a']);
        

        $today = convertToStamp(getNow());
        $day = substr($today,6,2);
        $mn = substr($today,4,2);
        $yr = substr($today,0,4);
        $thirtydays = date('Y-m-d', mktime('00', '00', '00', $mn-1, $day, $yr));
        
        $rs = $this->db->fetchArray("SELECT hit_page, 
            COUNT(hit_page) AS 'total' 
            FROM ".PX."stats 
            GROUP by hit_page 
            ORDER BY total DESC");
        
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++
        
        // table for all our results
        $body = "<table cellpadding='0' cellspacing='0' border='0'>\n";
        $body .= "<tr class='top'>\n";
        $body .= "<th width='90%' class='toptext'><strong>".$this->lang->word('page')."</strong></th>\n";
        $body .= "<th width='10%' class='toptext'><strong>".$this->lang->word('visits')."</strong></th>\n";
        $body .= "</tr>\n";
        $body .= "</table>\n";
        
        // dynamic output for table
        $body .= "<table cellpadding='0' cellspacing='0' border='0'>\n";
        if (!$rs)
        {
            $body .= tr(td('No hits yet', "colspan='2'"));
        }
        else
        {
            foreach($rs as $ar) {
            $body .= tr(
                td($ar['hit_page'],"width='90%' class='cell-doc'").
                td($ar['total'],"width='10%' class='cell-mid'"),
                    row_color(" class='color'"));
            }
        }
        // end dynamic rows output
        $body .= "</table>\n";
        
        
        $this->template->body = $body;
        
        return;
    }
    

}
