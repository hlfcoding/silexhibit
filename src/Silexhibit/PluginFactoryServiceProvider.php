<?php

namespace Silexhibit;

use Silex\Application,
    Silex\ServiceProviderInterface;

use Doctrine\Common\Inflector\Inflector;

use Silexhibit\FactoryInterface;

class PluginFactoryServiceProvider implements ServiceProviderInterface, FactoryInterface
{
  protected $service_name = 'plugin.factory';

  protected $app_options;
  protected $plugins;

  protected $app;

  public function __construct()
  {
    $this->plugins = array();
  }

  public function register(Application $app)
  {
    $app[$this->service_name] = $this;
    $this->app_options = $app['app.opts'];
    $this->app = $app;
  }
  public function boot(Application $app)
  {
  }

  public function getInstanceById($id)
  {
    return $this->getPlugin($id);
  }

  public function getPlugin($name)
  {
    return isset($this->plugins[$name]) ? $this->plugins[$name] : null;
  }

  public function getNames()
  {
    return array_keys($this->plugins);
  }

  public function registerPlugin($name, $context)
  {
    $plugin = $this->getPlugin($name);
    if (!isset($plugin)) {
      $class_name = Inflector::classify($name).'Plugin';
      $class_path = "Silexhibit\\Plugins\\$class_name\\$class_name";
      $plugin = $this->plugins[$name] = new $class_path($name);
      $plugin->register($this->app, $context);
    }
    return $plugin;
  }

}
