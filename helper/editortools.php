<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Editor functions
 * @author edited by Peng Wang <peng@pengxwang.com>
 * @version 1.1
 * @package Indexhibit
 **/

/**
 * @todo ai caramba, tinymce
 * @param string
 * @param bool
 * @param string
 * @param string
 * @global array
 * @global array
 * @return string
 **/
function editorTools ($content = '', $advanced = false, $additional = '', $process = '')
{
    global $go, $default;
    $OBJ =& get_instance();
    if ($OBJ->access->prefs['writing'] !== 1) {
        $s = "<div class='col' style='margin-top:18px;'>\n";
        $s .= href("<img src='asset/img/bold.gif' alt'[]' id='ed_bold'  />",'#',"title='" . $OBJ->lang->word('bold') . "' class='btn btn-off' onmouseover=\"this.className='btn btn-over'\" onmouseout=\"this.className='btn btn-off'\" onClick=\"edInsertTag(edCanvas, 0);return false;\" width='20'");
        $s .= href("<img src='asset/img/italic.gif' alt'[]' id='ed_italic'  />",'#',"title='" . $OBJ->lang->word('italic') . "' class='btn btn-off' onmouseover=\"this.className='btn btn-over'\" onmouseout=\"this.className='btn btn-off'\" onClick=\"edInsertTag(edCanvas, 1);return false;\"");
        $s .= href("<img src='asset/img/under.gif' alt'[]' id='ed_under' />",'#',"title='" . $OBJ->lang->word('underline') . "' class='btn btn-off' onmouseover=\"this.className='btn btn-over'\" onmouseout=\"this.className='btn btn-off'\" onClick=\"edInsertTag(edCanvas, 3);return false;\"");
        $s .= "<img src=\"asset/img/line_spcr.gif\" border=\"0\">\n";
        $s .= href("<img src='asset/img/link.gif' alt'[]' />",'#',"title='" . $OBJ->lang->word('links manager') . "' class='btn btn-off' onmouseover=\"this.className='btn btn-over'\" onmouseout=\"this.className='btn btn-off'\" onClick=\"OpenWindow('?a=system&amp;q=links','popup','325','350','yes');return false;\"");
        if ($advanced == 1) {
            $s .= href("<img src='asset/img/files.gif' alt'[]' />",'#',"title='" . $OBJ->lang->word('files manager') . "' class='btn btn-off' onmouseover=\"this.className='btn btn-over'\" onmouseout=\"this.className='btn btn-off'\" onClick=\"OpenWindow('?a=system&amp;q=files','popup','700','465','yes');return false;\"");
        }
        $s .= "</div>\n";
        $s .= "<div class='col txt-right' style='margin-top:18px;'>\n";
        $s .= "&nbsp;$additional";
        $s .= "</div>\n";
        $s .= "<div class='cl'><!-- --></div>\n";
        $OBJ->template->add_js('alexking.quicktags.js');
        $s .= "<textarea name='content' class='content' id='jxcontent' style='width:625px;'>" . stripForForm($content, $process) . "</textarea>\n";
        $s .= "<script type='text/javascript'>var edCanvas = document.getElementById('jxcontent');</script>\n";
    } else {
        $OBJ->template->add_extended_js('extend/tiny_mce/tiny_mce.js');
        $OBJ->template->add_script = "<script language='javascript' type='text/javascript'>
        <!--
        var action = '$go[a]';
        var ide = '$go[id]';
        var tinymce = true;
        tinyMCE.init({
            mode : 'textareas',
            theme : 'advanced',
            theme_advanced_toolbar_location : 'top',
            theme_advanced_layout_manager: 'SimpleLayout',
            theme_advanced_toolbar_align : 'left',
            theme_advanced_buttons1 : 'bold, italic, underline, separator, forecolorpicker, backcolorpicker, separator, justifyleft, justifycenter, justifyright, separator, link, unlink, separator, cleanup',
            theme_advanced_buttons2 : '',
            theme_advanced_buttons3 : '',
            force_br_newlines : true,
            convert_fonts_to_spans : true
        });
        //-->
        </script>";
        $s = "<div class='col' style='margin-top:18px;'>\n";
        $s .= "<div class='some1'><div class='top1'></div></div>\n";
        //$s .= "&nbsp;";
        $s .= "</div>\n";
        $s .= "<div class='col txt-right' style='margin-top:18px;'>\n";
        $s .= "&nbsp;$additional";
        $s .= "</div>\n";
        $s .= "<div class='cl'><!-- --></div>\n";
        $s .= "<textarea name='content' class='content' style='width:625px;' id='jxcontent'>" . stripForForm($content, $process) . "</textarea>";
    }
    return $s;
}

/**
 * @param string
 * @global array
 * @return string
 **/
function editorButtons ($published = '')
{
    global $go;
    $OBJ =& get_instance();
    $published = (isset($published)) ? $published : '';
    $s = "<input name='preview' type='image' src='asset/img/f-prev.gif' title='Preview (without saving)' class='btn btn-off' onmouseover=\"this.className='btn btn-over'\" onmouseout=\"this.className='btn btn-off'\" style='margin-bottom:0;' onclick=\"previewText($go[id]); return false;\" />\n";
    // save things
    $s .= "<input name='save' type='image' src='asset/img/save.gif' title='" . $OBJ->lang->word('save preview') . "'  class='btn btn-off' onmouseover=\"this.className='btn btn-over'\" onmouseout=\"this.className='btn btn-off'\" style='margin-bottom:0;' onclick=\"updateText($go[id]); return false;\" />\n";
    // delete things
    if ($go['id'] !== 1) {
        $s .= "<input name='delete' type='image' src='asset/img/delete.gif' title='" . $OBJ->lang->word('delete') . "' onClick=\"javascript:return confirm('" . $OBJ->lang->word('are you sure') . "');return false;\" class='btn btn-off' onmouseover=\"this.className='btn btn-over'\" onmouseout=\"this.className='btn btn-off'\" style='margin-bottom:0;' />\n";
    }
    return $s;
}
