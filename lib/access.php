<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Access class
 * User authentications
 * @version 1.1
 * @package Indexhibit
 * @subpackage Indexhibit CMS
 * @author Vaska 
 * @author Peng Wang <peng@pengxwang.com>
 **/
class Access 
{
    public $settings       = array();
    public $prefs          = array();
    public $cookie_expires;
    
    const DEFAULT_COOKIE_EXPIRES = 2; // 2 days

    public function __construct ($cookie_expires = null) {
        $this->cookie_expires = 3600 * 24 * (isset($cookie_expires) 
            ? $cookie_expires : self::DEFAULT_COOKIE_EXPIRES);
    }
        
    /**
     * User logout action
     **/
    public function logout ()
    {
        $cookie_expires = time() + $this->cookie_expires;
        setcookie('ndxz_hash', '', $cookie_expires);
        setcookie('ndxz_access', '', $cookie_expires);
        setcookie('ndxz_hash', '', $cookie_expires, '/');
        setcookie('ndxz_access', '', $cookie_expires, '/');
        $self = (dirname($_SERVER['PHP_SELF']) === '/') ? '' : dirname($_SERVER['PHP_SELF']);
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $self . '/');
    }

    /**
     * Settings action
     **/
    public function settings ()
    {
        $OBJ =& get_instance();
        $adm['adm_id'] = 1;
        $this->settings = $OBJ->db->selectArray('setting', $adm, Db::FETCH_RECORD);   
        if (!$this->settings) {
            show_error('error finding settings');
        }
    }
    
    /**
     * TODO description
     **/
    public function checkLogin ()
    {
        $OBJ =& get_instance();
        // if logging out
        if (isset($_POST['logout'])) {
            $this->logout();
        }
        $cookie_expires = time() + $this->cookie_expires;
        // if logging in
        if (isset($_POST['submitLogin'])) {
            sleep(3); // obscure prevention of absuse
            $clean['userid']    = getPOST('uid', null, 'password', 12);
            $clean['password']  = md5(getPOST('pwd', null, 'password', 12));
            $this->prefs = $OBJ->db->selectArray('user', $clean, Db::FETCH_RECORD);
            if ($this->prefs) {
                // create a new user hash upon login
                $temp['user_hash'] = md5(time() . $clean['password'] . 'secret');
                $OBJ->db->updateArray('user', $temp, "ID = {$this->prefs['ID']}");
                setcookie('ndxz_hash', $temp['user_hash'], $cookie_expires, '/');
                setcookie('ndxz_access', $clean['password'], $cookie_expires, '/');
                $this->settings();
                return;
            } else {
                show_login('login err');
            }
        }
        // return access
        if (isset($_COOKIE['ndxz_access']) && isset($_COOKIE['ndxz_hash'])) {
            $clean['user_hash'] = getCOOKIE($_COOKIE['ndxz_hash'], null, 'password', 32);
            $clean['password']  = getCOOKIE($_COOKIE['ndxz_access'], null, 'password', 32);
            $this->prefs = $OBJ->db->selectArray('user', $clean, Db::FETCH_RECORD);
            if ($this->prefs) {   
                // we'll update each time so no more weird logouts
                setcookie('ndxz_hash', $clean['user_hash'], $cookie_expires, '/');
                setcookie('ndxz_access', $clean['password'], $cookie_expires, '/');
                $this->settings();
                return;
            }
        }
        show_login();
    }   
}
