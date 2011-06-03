<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Check if a path exists and returns the mapped value
 * @global array<string>
 * @param string
 * @return string
 **/
function load_path($name)
{
    global $core_paths;
    if (array_key_exists($name, $core_paths)) {
        return $core_paths[$name];
    } else {
        throw new RuntimeException("$name is not a valid path.");
    }
}

/**
 * TODO description
 * @global Core
 * @global Core
 * @return Core
 **/
function get_instance()
{
    global $INDX, $OBJ;
    return is_object($INDX) ? $INDX : $OBJ;
}
 
/**
 * Autoloader
 * @global array Config
 * @static array<object>
 * @param string
 * @param bool optional
 * @param string
 * @param bool optional
 * @return object
 * @todo allow instantiation with parameters
 * @todo allow only driver to be database software specific
 **/
function &load_class ($class, $instantiate = true, $type, $internal = false)
{
    global $indx;
    static $objects = array();
    
    $path = DS . load_path($type) . DS;
    $file = $class;
    if ($type === 'db') {
        require_once DIRNAME . BASENAME . $path . "driver.{$indx['sql']}.php";
        $file = "db.{$indx['sql']}";
    }
    if ($type === 'lang') {
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
            $file = DIRNAME . BASENAME . $path . $sub_path . $file;
        }
        if (file_exists($file)) {
            require_once $file;
        } else {
            show_error('class not found');
        }
        if ($instantiate === true) {
            $objects[$class] = new $className();
        } else {
            $objects[$class] = true;
        }
    }
    
    return $objects[$class];
}

/**
 * Load helper
 * @param string
 **/
function load_helper($file)
{
    if (empty($file)) {
        throw new RuntimeException('what helper file?');
        return;
    }
    $file = DIRNAME . BASENAME . DS . HELPATH . DS . "$file.php";
    if (file_exists($file)) {
        require_once $file;
    }
}

/**
 * Load multiple helpers
 * @param array<string>
 **/
function load_helpers($files)
{
    if (empty($files)) {
        throw new RuntimeException('what helper files?');
        return;
    }
    foreach ($files as $file) {
        load_helper($file);
    }
}

/**
 * Load helper in module folder
 * @param string 
 * @param string 
 **/
function load_module_helper($file, $section)
{
    if (empty($file)) {
        throw new RuntimeException('what module helper file?');
        return;
    }
    $ds = DS;
    $file = DIRNAME . BASENAME . DS . MODPATH . DS . "${section}${ds}${file}.php";
    if (file_exists($file)) {
        require_once $file;
    }
}

/**
 * Show templated error and stop app
 * @param string
 * @param bool Show stack trace
 **/
function show_error($message = '', $trace = false)
{
    if (MODE === DEVELOPMENT || $trace) {
        _log(debug_backtrace(), 'stack trace');
    }
    // we'll use the default language for this
    $lang =& load_class('lang', true, 'lib');
    $lang->setlang(); // get the default strings
    $message = $lang->word($message);
    $error =& load_class('error', true, 'lib');
    header('Status: 503 Service Unavailable'); // change to right error note
    echo $error->show_error($message);
    exit;
}


// could use refinement - rethink
function show_login($message = '')
{
    // we'll use the default language for this
    $lang =& load_class('lang', true, 'lib');
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
    
    $error =& load_class('error', true, 'lib');
    echo $error->show_login($login);
    exit;
}

/**
 * Redirect function
 * @param string Query vars segment like `?a=action&q=note&id=0`
 * @todo do we need to put some validators on this?
 **/
function system_redirect ($params = '')
{
    // don't want the extra slash
    $self = (dirname($_SERVER['PHP_SELF']) === '/') ? '' : dirname($_SERVER['PHP_SELF']);    
    header("Location: http://{$_SERVER['HTTP_HOST']}$self/$params");
    return;
}

/**
 * Frontend route parser
 * @param string
 * @param string
 * @return string
 * @todo refine
 **/
function entry_uri ($uri = '', $server_uri)
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
                if ($url[0] === $dir) {
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

/**
 * For dealing with modrewrite issues
 * @param string
 * @return string
 **/
function ndxz_rewriter ($url = '') {
    if (MODREWRITE === false) {
        if ($url === '/') {
            return '/';
        } else {
            return '/index.php?' . $url;
        }
    } else {
        return $url;
    }
}

/**
 * Display a simple log message. Useful for debugging. 
 * Can be inserted anywhere.
 * @param mixed
 * @param string
 * @param bool
 * @return mixed
 **/
function _log ($data, $label = null, $trace = false) {
    if ($GLOBALS['default']['run_traces'] === false) {
        return $data;
    }
    require_once DIRNAME . BASENAME . DS . TESTPATH . DS . 'test.pcss';
    $label = isset($label) ? "$label: " : '';
    echo '<pre class="log">' . $label;
    var_export($data); 
    echo '</pre>';
    if ($trace) {
        _log(debug_backtrace(), 'stack trace');
    }
    return $data;
}
