<?php

namespace Silexhibit;

use Silex\Application;

use Silexhibit\Plugin\PluggableViewInterface;

abstract class Plugin
{
  public $name;
  public $config;

  protected $context;

  public function __construct($name)
  {
    $this->name = $name;
  }
  public function register(Application $app, $context)
  {
    if (isset($app["plugin.{$this->name}"])) {
      $this->config = $app["plugin.{$this->name}"];
    }
  }
  public function run($opts, $context=null)
  {
   $context = isset($context) ?: $this->context;
   if ($context instanceof PluggableViewInterface) {
      $view = $context;
      return $this->render($opts, $view);
    }
    return false;
  }
  protected function render($opts, PluggableViewInterface $view)
  {
    $templater = $view->getPluginTemplater();
    $base_content = array(
      'options' => $opts,
    );
    $content = $this->provideContent($view);
    $content['js_templates'] = $view->getJSTemplates($this->config['view'], $templater->getLoader(), "{$this->name}/partial");
    $content = isset($content) ? array_merge($base_content, $content) : $base_content;
    return $templater->render("{$this->name}/main", $content);
  }
  abstract protected function provideContent(PluggableViewInterface $view);
}
