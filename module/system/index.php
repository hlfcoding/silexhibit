<?php if (!defined('SITE')) exit('No direct script access allowed');


class System extends Router
{
    function __construct()
    {
        parent::__construct();
        
        // library of $_POST options
        $submits = array('upd_files', 'upd_file', 'upd_user');
        
        // from $_POST to method
        $this->posted($this, $submits);
    }
    
    
    function page_index()
    {
        $this->page_prefs();
    }
    
    
    function page_prefs()
    {
        global $go;

        $this->template->location_override = $this->lang->word('user');
        $this->template->location = $this->lang->word('preferences');
        
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++
        
        // the record
        $rs = $this->db->fetchRecord("SELECT * 
            FROM ".PX."users 
            WHERE ID = '".$this->access->prefs['ID']."'");
            
        load_helper('output');
        load_module_helper('files', $go['a']);
        
        $body = "<div class='c3 bg-grey'>\n";
        $body .= "<div class='col'>\n";
        
        $body .= ips($this->lang->word('login'), 'input', 'userid', $rs['userid'], "maxlength='12'", 'text', $this->lang->word('required').' '.$this->lang->word('number chars'), 'req');
        $body .= ips($this->lang->word('change password'), 'input', 'password', null, "maxlength='12'", 'password', $this->lang->word('required').' '.$this->lang->word('number chars'), 'req');
        $body .= ips($this->lang->word('confirm password'), 'input', 'cpassword', null, "maxlength='12'", 'password', $this->lang->word('if change'),'req');
        $body .= ips($this->lang->word('time now'), 'getTimeOffset', 'user_offset', $rs['user_offset']);
        $body .= ips($this->lang->word('time format'), 'getTimeFormat', 'user_format', $rs['user_format']);
        $body .= ips($this->lang->word('your language'), 'getLanguage', 'user_lang', $rs['user_lang'], null, 'text');
        
        $body .= input('huser_lang', 'hidden', null, $rs['user_lang']);
        
        $body .= input('upd_user', 'submit', null, $this->lang->word('update'));
        $body .= "</div>";
        $body .= "<div class='cl'><!-- --></div>";
        $body .= "</div>";
        
        $this->template->body = $body;
        
        return;
    }
    
    
    function page_logout()
    {
        $this->access->logout();
    }
    
    
    function page_files()
    {
        global $go;
        
        load_helper('html');
        load_module_helper('files', $go['a']);
        
        $this->template->add_js('modEdit.js');
        
        $this->template->pop_location = $this->lang->word('files manager');
        
        $this->template->pop_links[] = array($this->lang->word('upload files'), "?a=$go[a]&amp;q=upload");
        
        // ++++++++++++++++++++++++++++++++++++++
        
        $body = div(getFiles(), "id='p-files'");
        
        // need to clean this up
        $body .= "<div style='float: right; width: 400px; border: 1px solid #ccc; height: 300px; background: #f3f3f3;'>\n";
        $body .= "<iframe name='show' style='width: 400px; height: 300px; overflow: auto;'></iframe>\n";
        $body .= "</div>\n";
        
        $body .= "<div class='cl'><!-- --></div>\n";
        
        $body .= div(p('&nbsp;'));
        
        $this->template->body = $body;
        
        $this->template->output('popup');
        exit;
    }
    
    
    function page_links()
    {
        global $go;
        
        load_helper('html');
        load_module_helper('files', $go['a']);
        
        $this->template->add_js('alexking.quicktags.js');
        
        $this->template->pop_location = $this->lang->word('links manager');
        
        // ++++++++++++++++++++++++++++++++++++++
        
        $body = div(linksManager());
        $body .= input($this->lang->word('submit'), 'button', "onclick=\"edInsertSysLink(edCanvas, 2, sysLink.value);\"", 'Submit');
        
        $body .= "<p><strong>".$this->lang->word('create link')."</strong><br />\n";
        $body .= "<select name=\"selectType\" class=\"list\" style=\"width:225px;\">\n";
        $body .= "<option value=\"1\">".$this->lang->word('hyperlink')."</option>\n";
        $body .= "<option value=\"2\">".$this->lang->word('email')."</option>\n";
        $body .= "</select>\n";
        
        
        $body .= "<br />\n";
        $body .= $this->lang->word('urlemail')."<br />\n";
        $body .= "<input type=\"text\" name=\"enterLink\" class=\"txtfld\" style=\"width:225px;\" value='http://' /><br />\n";

        
        $body .= "<script type='text/javascript'>var edCanvas = window.opener.document.getElementById('jxcontent');</script>\n";
        
        $body .= "<input type=\"button\" value=\"Submit\" onclick=\"edInsertLink(edCanvas, 2, enterLink.value);\" id='ed_link' /></p>\n";
        
        $this->template->body = $body;
        
        $this->template->output('popup');
        exit;
    }
    
    
    function page_editfile()
    {
        global $go;
        
        load_helper('html');
        
        $this->template->pop_location = $this->lang->word('edit file info');
        
        $this->template->pop_links[] = array($this->lang->word('files manager'), "?a=$go[a]&amp;q=files");
        $this->template->pop_links[] = array($this->lang->word('upload'), "?a=$go[a]&amp;q=upload");
        
        // ++++++++++++++++++++++++++++++++++++++
        
        $rs = $this->db->fetchRecord("SELECT * FROM ".PX."media 
            WHERE media_id = '$go[id]' 
            AND media_obj_type = ''");
        
        if (!$rs)
        {
            $body = p($this->lang->word('none found'));
        }
        else
        {
            $body = "<h2>$rs[media_file]</h2>\n";
            $body .= br();

            $body .= ips($this->lang->word('description'), 'input', 'media_title', $rs['media_title'], "maxlength='35'", 'text');
            $body .= ips($this->lang->word('width'), 'input', 'media_x', $rs['media_x'], "maxlength='4'", 'text', $this->lang->word('if applicable'));
            $body .= ips($this->lang->word('height'), 'input', 'media_y', $rs['media_y'], "maxlength='4'", 'text', $this->lang->word('if applicable'));
            $body .= input('upd_editfile', 'submit', null, $this->lang->word('update'));
            $body .= input('upd_delfile','submit', "onclick=\"javascript:return confirm('".$this->lang->word('are you sure')."');return false;\"", $this->lang->word('delete'));
            $body .= input('upd_file', 'hidden', null, $this->lang->word('update'));
        }
        
        $this->template->body = $body;
        
        $this->template->output('popup');
        exit;
    }
    
    
    function page_upload()
    {
        global $go, $uploads;
        
        load_helper('html');
        load_module_helper('files', $go['a']);
        
        $this->template->pop_location = $this->lang->word('upload');
        
        $this->template->pop_links[] = array($this->lang->word('files manager'), "?a=$go[a]&amp;q=files");
        
        $this->template->form_type = true;
        
        // ++++++++++++++++++++++++++++++++++++++
        
        $body = createFileBox(5);
        $body .= input('upd_files', 'submit', null, $this->lang->word('submit'));
        
        // list of allowed filetypes
        $allowed = array_merge($uploads['images'], $uploads['media'], $uploads['files'], $uploads['flash']);
        
        $showus = array();
        
        if (is_array($allowed))
        {
            foreach ($allowed as $type) $showus[] = $type;
        }
        
        $body .= p("<strong>" . $this->lang->word('allowed filetypes') . ":</strong> " . implode(', ', $showus) . br () . "<strong>" . $this->lang->word('max file size') . ":</strong> " . getLimit(),
            "style='color:c00;'");
        
        $this->template->body = $body;
        
        $this->template->output('popup');
        exit;
    }
    
    
    function page_prv()
    {
        global $go;
        
        // query for our variables
        $rs = $this->db->fetchRecord("SELECT * 
            FROM ".PX."objects, ".PX."objects_prefs 
            WHERE id = '$go[id]' 
            AND object = obj_ref_type");
            
        if (!$rs) show_error('no results');
        
        // get plugins (all of them)
        include DIRNAME . BASENAME . DS . PLUGPATH . DS . 'index.php';
        
        // additional parts
        $rs['baseurl'] = BASEURL;
        $rs['basename'] = BASENAME;
        $rs['basefiles'] = BASEFILES;
        $rs['gimgs'] = GIMGS;
        
        // why do i need this for front?
        $GLOBALS['rs'] = $rs;
        
        // get the front end helper class
        load_helpers(array('time'));
        $this->lib_class('front');
        
        // time for some action
        if ($rs['obj_theme'] == 'eatock')
        {
            $contents = $this->front->front_eatock();
        }
        else
        {
            $filename = DIRNAME . BASENAME . DS . SITEPATH . DS . $rs['obj_theme'] . '/index.php';
            $fp = @fopen($filename, 'r');
            $contents = fread($fp, filesize($filename));
            fclose($fp);
        }

        // parse it
        $PARSE =& load_class('parse', true, 'lib');
        $PARSE->vars = $rs;
        $PARSE->code = $contents;
        echo $PARSE->parsing();
        exit;
    }
    
    
    
    function sbmt_upd_user()
    {
        global $go;
        $processor =& load_class('processor', true, 'lib');

        $clean['user_offset'] = $processor->process('user_offset', array('digit'));
        $clean['user_format'] = $processor->process('user_format', array('notags'));
        $clean['user_lang'] = $processor->process('user_lang', array('notags'));
        
        // we need to validate userid and password for non latin-1 chars too
        $clean['userid'] = $processor->process('userid',array('pchars', 'length12','notags', 'reqNotEmpty'));
        $check['password'] = $processor->process('password',array('pchars', 'length12', 'notags'));
        $check['cpassword'] = $processor->process('cpassword',array('pchars', 'length12', 'notags'));
        
        // need to check for change in password...
        if ($check['password'] !== '')
        {
            if ($check['password'] === $check['cpassword'])
            {
                $clean['password'] = md5($check['password']);
                
                // reset access so we don't logout
                setcookie('ndxz_access', $clean['password'], time()+3600*24*2, '/');
            }
            else
            {
                $processor->force_error();
                $this->template->action_error = 'passwords do not match';
            }
        }


        if ($processor->check_errors())
        {
            // get our error messages
            $error_msg = $processor->get_errors();
            $this->errors = true;
            $GLOBALS['error_msg'] = $error_msg;
            return;
        }
        else
        {
            $this->db->updateArray(PX.'users', $clean, "ID='".$this->access->prefs['ID']."'");
        }
        
        // if change in language we need to refresh
        if ($clean['user_lang'] !== $temp['huser_lang']) system_redirect("?a=$go[a]");

        // send an update notice
        $this->template->action_update = 'updated';
    }
    
    
    
    function sbmt_upd_file()
    {
        global $go;
        
        if (isset($_POST['upd_delfile']))
        {
            $file = $this->db->fetchRecord("SELECT media_id,media_file FROM ".PX."media 
                WHERE media_id='$go[id]'");
            
            if ($file)
            {
                // let's at least force it out of the database
                $this->db->deleteArray(PX.'media', "media_id='$go[id]'");
            
                if (file_exists(DIRNAME . '/files/' . $file['media_file']))
                {
                    //$this->db->deleteArray(PX.'media', "media_id='$go[id]'");
                    unlink(DIRNAME . '/files/' . $file['media_file']);
                }
            }
        }
        else
        {
            $processor =& load_class('processor', true, 'lib');
            
            $clean['media_title'] = $processor->process('media_title',array('notags'));
            $clean['media_x'] = $processor->process('media_x',array('digit'));
            $clean['media_y'] = $processor->process('media_y',array('digit'));


            if ($processor->check_errors())
            {
                // get our error messages
                $error_msg = $processor->get_errors();
                $this->errors = true;
                $GLOBALS['error_msg'] = $error_msg;
                return;
            }
            else
            {
                $this->db->updateArray(PX.'media', $clean, "media_id='$go[id]'"); 
            }
        }
        
        system_redirect("?a=$go[a]&q=files");
    }
    
    
    // upload files
    function sbmt_upd_files()
    {
        $OBJ->template->errors = true;
        global $go, $uploads;

        $IMG =& load_class('media', true, 'lib');
            
        // +++++++++++++++++++++++++++++++++++++++++++++++++++
        
        // need to rewrite this too

        $num = count($_FILES['filename']['name']);
        $dir = DIRNAME . BASEFILES . '/';
        $types = array_merge($uploads['images'], $uploads['media'], $uploads['files'], $uploads['files'], $uploads['flash']);
        $IMG->path = $dir;
            
        if ($num > 0)
        {
            for ($i = 0; $i < $num; $i++)
            {
                if ($_FILES['filename']['size'][$i] < $IMG->upload_max_size)
                {
                    $title  = (isset($_POST['media_title'][$i])) ? $_POST['media_title'][$i] : '';
                    
                    // we need to clean the file name
                    $test = explode('.', $_FILES['filename']['name'][$i]);
                    $thetype = array_pop($test);
                    
                    load_helper('output');
                    $URL =& load_class('publish', true, 'lib');

                    $URL->title = implode('_', $test);
                    $name = $URL->processTitle();
                    
                    // look for dupllications
                    $name = ($name == '') ? time().$i : $name;
                    
                    $IMG->type = '.' . $thetype;
                    $IMG->filename = $IMG->checkName($name) . '.' . $thetype;
                    
                    if (in_array($thetype,$types))
                    {
                        // if uploaded we can work with it
                        if (move_uploaded_file($_FILES['filename']['tmp_name'][$i], $IMG->path.'/'.$IMG->filename)) 
                        {
                            $clean['media_id']  = 'null';
                            $clean['media_file'] = $IMG->filename;
                            $clean['media_uploaded'] = getNow();
                            $clean['media_udate'] = getNow();
                            $clean['media_kb']  = str_replace('.', '', filesize($IMG->path . '/' . $IMG->filename));
                            $clean['media_title'] = $title;
                            $clean['media_mime'] = $thetype;
                        
                            // only images can deal with these
                            if (in_array($thetype, $uploads['images']))
                            {
                                $size = getimagesize($IMG->path . '/' . $IMG->filename);
                                $clean['media_x'] = $size[0];
                                $clean['media_y'] = $size[1];
                            }
                            
                            $this->db->insertArray(PX.'media', $clean);
                            
                            @chmod($IMG->path . '/' . $IMG->filename, 0755);
                        }
                        else
                        {
                            // file not uploaded
                        }
                    }
                    else
                    {
                    // not a valid format
                    }
                }
                else
                {
                    // too big
                }
            }
        }
            
        system_redirect("?a=$go[a]&q=files");
    }
    
}

