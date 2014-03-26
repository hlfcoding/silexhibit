<?php

namespace Silexhibit;

use Silex\Application,
    Silex\ServiceProviderInterface;

use Silexhibit\FactoryInterface;

abstract class WidgetFactoryServiceProvider implements ServiceProviderInterface, FactoryInterface
{
  protected $app_options;
  protected $widgets;

  protected $app;

  public function __construct()
  {
    $this->widgets = array();
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
    return $this->getWidget($id);
  }

  public function getWidget($name)
  {
    return isset($this->widgets[$name]) ? $this->widgets[$name] : null;
  }

  public function getNames()
  {
    return array_keys($this->widgets);
  }

  public function registerWidget($name, $widget)
  {
    $this->widgets[$name] = $widget;
  }

  abstract public function generateWidget($data);
}
