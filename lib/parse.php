<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
 * Parse class
 * Used to parse frontend template for output
 * @version 1.1
 * @package Indexhibit
 * @author Vaska 
 * @author Peng Wang <peng@pengxwang.com>
 **/

class Parse
{
    public $plugins;
    public $cache_enabled;
    public $code;
    public $vars;

    /**
     * @return string
     **/
    public function parsing ()
    {
        // twice so we can have plugins and vars in our content var
        $output = $this->parser($this->doVariables($this->code));
        $output = $this->parser($this->doVariables($output));
        return preg_replace('|<\?[php]?(.)*\?>|sUi', '', $output);
    }
    /**
     * @param string
     * @return string callbacked
     **/
    public function parser ($text)
    {
        $f = '/<plug:(\S+)\b(.*)(?:(?<!br )(\/))?' . chr(62) . '(?(3)|(.+)<\/plug:\1>)/sUi';
        return preg_replace_callback($f, array($this, 'processTags'), $text);
    }
    /**
     * @param string $text
     * @return string callbacked
     **/
    public function doVariables ($text)
    {
        $f = '|<%(.*)%>|sUi';
        return preg_replace_callback($f, array($this, 'getVar'), $text);
    }
    /**
     * @param string $name
     * @return string
     **/
    public function getVar ($name)
    {
        $theVar = trim($name[1]);
        if ($theVar === 'tags') {
            $this->vars[$theVar] = $this->converTags($this->vars[$theVar]);
        }
        return (!$this->vars[$theVar]) ? '' : $this->vars[$theVar];
    }
    /**
     * @param string
     * @return string
     **/
    public function converTags ($input)
    {
        if ($input === '') {
            return null;
        }
        $tags_array = implode('|', explode(',', $input));
        return $tags_array;
    }
    /**
     * @param array<string>
     * @return string
     **/
    public function processTags ($match)
    {
        $this->func = $match[1];
        $arg_list = func_get_args();
        if ($arg_list[0][2] !== '') {
            $args = explode(',', $arg_list[0][2]);
            $args = array_map('trim', $args);
        } else {
            $args = null;
        }
        $args = $this->getArgs($args);
        if (function_exists($this->func)) {
            return call_user_func_array($this->func, $args);
        } else {
            return;
        }
    }
    /**
     * @param array $args
     * @return string parameters for function
     **/
    public function getArgs ($args)
    {
        if (!isset($args)) {
            return;
        }
        foreach ($args as $arg) {
            // var
            $arg = preg_replace('/^.*=/', '', $arg);
            // front
            $arg = preg_replace('/^(\'|")/', '', $arg);
            // back
            $var[] = preg_replace('/(\'|")$/', '', $arg);
        }
        return $var;
    }
}
