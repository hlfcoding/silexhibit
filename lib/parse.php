<?php if (!defined('SITE')) exit('No direct script access allowed');

/**
* Parse class
*
* Used to parse frontend template for output
* 
* @version 1.0
* @author Vaska 
*/
class Parse
{
    var $plugins;
    var $cache_enabled;
    var $code;
    var $vars;

    /**
    * Returns string
    *
    * @param void
    * @return string
    */
    function parsing()
    {
        // twice so we can have plugins and vars in our content var
        $output = $this->parser($this->doVariables($this->code));
        $output = $this->parser($this->doVariables($output));
        
        return preg_replace('|<\?[php]?(.)*\?>|sUi','',$output);
    }

    /**
    * Returns callback'd string
    *
    * @param string $text
    * @return string
    */
    function parser($text)
    {
        $f = '/<plug:(\S+)\b(.*)(?:(?<!br )(\/))?'.chr(62).'(?(3)|(.+)<\/plug:\1>)/sUi';
        return preg_replace_callback($f,array($this,'processTags'),$text);
    }


    /**
    * Returns callback'd system variables
    *
    * @param string $text
    * @return string
    */
    function doVariables($text)
    {
        $f = '|<%(.*)%>|sUi';
        return preg_replace_callback($f,array($this,'getVar'),$text);
    }


    /**
    * Returns system variables
    *
    * @param string $name
    * @return string
    */
    function getVar($name)
    {
        $theVar = trim($name[1]);

        if ($theVar == 'tags') $this->vars[$theVar] = $this->converTags($this->vars[$theVar]);
        
        return (!$this->vars[$theVar]) ? '' : $this->vars[$theVar];
    }


    /**
    * Returns adjusted array for tags
    * (we aren't using this right now)
    *
    * @param string $input
    * @return string
    */
    function converTags($input)
    {
        if ($input == '') return NULL;
        $tags_array = implode('|',explode(',',$input));
        return $tags_array;
    }


    /**
    * Returns callback for function
    *
    * @param array $match
    * @return string
    */
    function processTags($match)
    {
        $this->func = $match[1];
        $arg_list = func_get_args();
        if ($arg_list[0][2] != '') {
            $args = explode(',',$arg_list[0][2]);
            $args = array_map('trim',$args);
        } else {
            $args = NULL;
        }

        $args = $this->getArgs($args);

        if (function_exists($this->func)) {
            return call_user_func_array($this->func,$args);
        } else {
            return;
        }
    }

    /**
    * Returns parameters for function
    *
    * @param array $args
    * @return string
    */
    function getArgs($args)
    {
        if ($args == NULL) return;

        foreach ($args as $arg)
        {
            // var
            $arg = preg_replace('/^.*=/','',$arg);
            // front
            $arg = preg_replace('/^(\'|")/','',$arg);
            // back
            $var[] = preg_replace('/(\'|")$/','',$arg);
        }

        return $var;
    }
}
