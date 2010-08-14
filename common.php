<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * TODO description
 * @param type optional Description
 * @return void
 **/
function load_path($name)
{
    global $core_paths;
    if (in_array($name, $core_paths)) {
        return $name;
    } else {
        throw new RuntimeException("$name is not a valid path.");
    }
}

/**
 * TODO description
 * @param type optional Description
 * @return void
 **/
function get_instance()
{
    global $INDX, $OBJ;
    return is_object($INDX) ? $INDX : $OBJ;
}
 
/**
 * Autoloader
 * @param string
 * @param bool optional
 * @param string
 * @param bool optional
 * @return object
 **/
function &load_class ($class, $instantiate = true, $type, $internal = false)
{
    global $indx;
    static $objects = array();
    
    $path = DS . load_path($type) . DS;
    $file = $class;
    if ($type == 'db') {
        $file = 'db.' . $indx['sql'];
    }
    if ($type == 'lang') {
        $file = 'index';
    }
    $sub_path = $file . DS;
    $file .= '.php';
    $className = ucfirst($class);

    if (!isset($objects[$class])) {
        if ($internal === false) {
            $file = DIRNAME . BASENAME . $path . $file;
        } else {
            $file = 'index.php';
            $file = DIRNAME . BASENAME . $path . $subpath . $file;
        }
        if (file_exists($file)) {
            require_once $file;
        } else {
            show_error('class not found');
        }
        if ($instantiate == TRUE) {
            $objects[$class] = new $className();
        } else {
            $objects[$class] = TRUE;
        }
    }
    
    return $objects[$class];
}


// from the helper folder
function load_helper($file)
{
    if ($file == '') return;
    
    if (file_exists(DIRNAME . BASENAME . '/' . HELPATH . '/' . $file . '.php'))
    {
        require_once(DIRNAME . BASENAME . '/' . HELPATH . '/' . $file . '.php');
    }
}

/**
 * Load multiple helpers
 * @param array
 * @return void
 **/
function load_helpers($files)
{
    if (!is_array($files)) {
        return;
    }
    foreach ($files as $file) {
        load_helper($file);
    }
}

/**
 * Load helpers in module folder
 * @param string 
 * @param string 
 * @return void
 **/
function load_module_helper($file, $section)
{
    if (empty($file)) {
        return;
    }
    if (file_exists(DIRNAME . BASENAME . '/' . MODPATH . '/' . $section . '/' . $file . '.php')) {
        require_once(DIRNAME . BASENAME . '/' . MODPATH . '/' . $section . '/' . $file . '.php');
    }
}


function show_error($message = '', $backtrace = false)
{
    if (MODE === DEVELOPMENT || $backtrace) {
        _log(debug_backtrace(), 'stack trace');
    }
    // we'll use the default language for this
    $lang =& load_class('lang', TRUE, 'lib');
    $lang->setlang(); // get the default strings
    $message = $lang->word($message);
    $error =& load_class('error', TRUE, 'lib');
    header('Status: 503 Service Unavailable'); // change to right error note
    echo $error->show_error($message);
    exit;
}


// could use refinement - rethink
function show_login($message='')
{
    // we'll use the default language for this
    $lang =& load_class('lang', TRUE, 'lib');
    $lang->setlang(); // get the default strings
    
    $login = "<form method='post' action=''>
    <h1>Indexhibit</h1>
    <br />
    <p><strong>".$lang->word('login').":</strong> (".$lang->word('number chars').") 
        <input name='uid' type='text' maxlength='12' /></p>
    <p><strong>".$lang->word('password').":</strong> (".$lang->word('number chars').") 
        <input name='pwd' type='password' maxlength='12' /></p>
    <p><input name='submitLogin' type='submit' value='".$lang->word('login')."' class='login-button' /></p>
    <p>".$lang->word($message)."&nbsp;</p>
    </form>";
    
    $error =& load_class('error', TRUE, 'lib');
    echo $error->show_login($login);
    exit;
}


/* system_redirect("?a=$go[a]&q=note&id=$last"); */
function system_redirect ($params='')
{
    // do we need to put some validators on this?
    // don't want the extra slash
    $self = (dirname($_SERVER['PHP_SELF']) == '/') ? '' : dirname($_SERVER['PHP_SELF']);
    
    header('Location: http://' . $_SERVER['HTTP_HOST'] . $self . '/' .  $params);
    return;
}


// revise this later...
function entry_uri ($uri='', $server_uri)
{
    $url = $server_uri;

    // remove any illegal chars first ' " $ * @
    // remove non alpha chars (a-zA-Z0-9-/?# only)
    // all urls are lowercase
    $url = preg_replace(
        array("/[^-_a-zA-Z0-9?=#\/]/", '/\/+/'),
        array('','/'), $url);
        
    $url = strtolower($url);

    // we need to remove the references (they can be found when necessary)
    $url = preg_replace("/\?(.*)$/", '', $url);

    $url = explode('/', $url);
    if (is_array($url)) {
        array_shift($url);
    }
    // if we aren't in the root we need to deal with it
    // allows our site to be more portable
    $uri = preg_replace(array("/^\//","/\/$/"), array('', ''), $uri);

    if ($uri !== '') {
        $delete_dir = explode('/', $uri);
        // we need to pop off the default root if it's set
        if (is_array($delete_dir)) {
            foreach ($delete_dir as $dir) {
                if ($url[0] == $dir) {
                    array_shift($url);
                }
            }
        }
    }

    // always must have / at the beginning for the db
    $url = '/'.implode('/', $url);

    // ouch, needs thought
    // trailing slash
    if ((substr($url, -1) !== '/') && 
        (substr($url, -3) !== 'php')) {
        $url = $url . '/';
    }

    return $url;
}

// for dealing with mod_rewrite issues
function ndxz_rewriter($url='') {
    if (MODREWRITE === false) {
        if ($url == '/') {
            return '/';
        } else {
            return '/index.php?' . $url;
        }
    } else {
        return $url;
    }
}

function _log ($data, $label = null) {
    require_once DIRNAME . BASENAME . DS . TESTPATH . DS . 'test.pcss';
    $label = isset($label) ? '' : "$label: ";
    echo '<pre class="log">' . $label;
    var_export($data); 
    echo '</pre>';
}
