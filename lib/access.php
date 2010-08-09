<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Access class
*
* User authentications
* 
* @version 1.0
* @author Vaska 
*/
class Access 
{
    var $settings       = array();
    var $prefs          = array();
    
    /**
    * User logout 
    *
    * @param void
    * @return mixed
    */
    function logout()
    {
        setcookie('ndxz_hash', '', time()+3600*24*2);
        setcookie('ndxz_access', '', time()+3600*24*2);
        setcookie('ndxz_hash', '', time()+3600*24*2, '/');
        setcookie('ndxz_access', '', time()+3600*24*2, '/');
        
        $self = (dirname($_SERVER['PHP_SELF']) == '/') ? '' : dirname($_SERVER['PHP_SELF']);
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $self . '/');
    }

    /**
    * Returns settings array or error
    *
    * @param void
    * @return mixed
    */
    function settings()
    {
        $OBJ =& get_instance();
        
        $adm['adm_id'] = 1;
        
        $this->settings = $OBJ->db->selectArray(PX.'settings', $adm, 'record');
            
        if (!$this->settings) show_error('error finding settings');
    }
    
    /**
    * Returns user preferences array or error
    *
    * @param void
    * @return mixed
    */
    function checkLogin()
    {
        $OBJ =& get_instance();
    
        // if logging out
        if (isset($_POST['logout'])) $this->logout();
        
        // if logging in
        if (isset($_POST['submitLogin'])) 
        {
            sleep(3); // obscure prevention of absuse
            
            $clean['userid']    = getPOST('uid', null, 'password', 12);
            $clean['password']  = md5(getPOST('pwd', null, 'password', 12));

            $this->prefs = $OBJ->db->selectArray(PX.'users', $clean, 'record');
                
            if ($this->prefs)
            {
                // create a new user hash upon login
                $temp['user_hash'] = md5(time() . $clean['password'] . 'secret');

                $OBJ->db->updateArray(PX.'users', $temp, "ID='".$this->prefs['ID']."'");
                
                setcookie('ndxz_hash', $temp['user_hash'], time()+3600*24*2, '/');
                setcookie('ndxz_access', $clean['password'], time()+3600*24*2, '/');

                $this->settings();
                return;
            }
            else
            {
                show_login('login err');
            }
        }


        // return access
        if (isset($_COOKIE['ndxz_access']) && isset($_COOKIE['ndxz_hash'])) 
        {
            $clean['user_hash'] = getCOOKIE($_COOKIE['ndxz_hash'], null, 'password', 32);
            $clean['password']  = getCOOKIE($_COOKIE['ndxz_access'], null, 'password', 32);

            $this->prefs = $OBJ->db->selectArray(PX.'users', $clean, 'record');
                
            if ($this->prefs)
            {   
                // we'll update each time so no more weird logouts
                setcookie('ndxz_hash', $clean['user_hash'], time()+3600*24*2, '/');
                setcookie('ndxz_access', $clean['password'], time()+3600*24*2, '/');
                
                $this->settings();
                return;
            }
        }
        
        show_login();
    }   
}
