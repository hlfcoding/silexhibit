<?php

namespace Silexhibit\Plugin;

use Silexhibit\Plugin\PluggableViewInterface;
use array_column;

trait PluggableViewTrait
{
  protected $plugins;
  protected static $plugin_r_tag = '/<plugin:(\S+)([^\/>]*)\/?>(?:(.*)<\/plugin:\1>)?/is';

  protected function runPlugins()
  {
    if (!($this instanceof PluggableViewInterface)) {
      throw new \Exception("Class needs to implement required interface.", 1);
    }
    $html = $this->getPluggableHtml();
    $html = preg_replace_callback($this::$plugin_r_tag, function ($match) {
      list($tag, $name, $attrs) = $match;
      if (isset($match[3])) {
        $inner_html = $match[3];
      }
      $attrs = array_filter(explode(' ', $attrs),
        function ($attr) { return !empty($attr); });
      $opts = array();
      foreach ($attrs as $attr) {
        list($key, $value) = array_map(function ($piece) { return trim($piece); },
          explode('=', $attr));
        $opts[$key] = $value;
      }
      return $this->loadPlugin($name)->run($opts);
    }, $html);
    $this->setPluggableHtml($html);
  }

  protected function loadPlugin($name)
  {
    if (!isset($this->plugins)) {
      throw new \Exception("No plugin factory.", 1);
    }
    $plugin = $this->plugins->getPlugin($name);
    if (isset($plugin)) {
      return;
    }
    $this->addAssetsForPlugin($name);
    return $this->plugins->registerPlugin($name, $this);
  }

}
