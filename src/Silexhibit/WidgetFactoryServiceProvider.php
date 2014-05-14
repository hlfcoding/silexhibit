<?php

// Silexhibit Widget Factory
// =========================
// This is one of Silexhibit's component factory service providers. Its main
// responsibilities are creating, indexing, and storing widget instances.
// Conventionally, these factories are created and registered as service
// providers by the specific controller.

// Note that this class should be subclassed so `generateWidget` can be
// implemented. Conventionally, widgets are generated as an array of data.
// Conventional keys include `name`, `content`, `config`.

// Conceptually, a widget is a component that can be shared between views. The
// view decides how to integrate and render the widget's content, for which
// there is no strict interface to implement. Therefore there is a many-many
// relationship between exhibit factories and views, although the factories
// don't keep actual references to the views.

namespace Silexhibit;

use Silex\Application,
    Silex\ServiceProviderInterface;

use Silexhibit\FactoryInterface;

abstract class WidgetFactoryServiceProvider implements ServiceProviderInterface, FactoryInterface
{
  // Core Configuration
  // ------------------

  // For the plugin factory to be able to register as a Silex service provider,
  // `service_name` must be set before calling the constructor. Conventionally,
  // it can be set by redeclaring the property. Conventionally, dot notation
  // should be used.

  // Convenience reference to the `app.opts` global.
  protected $app_options;
  // Convenience reference to the `app` global. Storing this makes it easier to
  // register widgets.
  protected $app;

  // Widget Management
  // -----------------

  // `widgets` stores the widget instances, with the keys being their name
  // (`id`). This internal structure makes the public API relatively
  // straightforward.
  protected $widgets;

  public function __construct()
  {
    $this->widgets = array();
  }

  public function register(Application $app)
  {
    // - Alias services and globals.
    $this->app_options = $app['app.opts'];
    $this->app = $app;
    // - Publish self as service.
    $app[$this->service_name] = $this;
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

  // `generateWidget` should return the widget array given the provided `data`.
  // It should use `getWidget` and only generate as needed.
  abstract public function generateWidget($data);
}
