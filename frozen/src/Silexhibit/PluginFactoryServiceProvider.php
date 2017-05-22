<?php

// Silexhibit Plugin Factory
// =========================
// This is one of Silexhibit's component factory service providers. Its main
// responsibilities are creating, indexing, and storing plugin instances.
// Conventionally, these factories are created and registered as service
// providers by the specific controller.

// Plugin names should be in the format of `foo-bar`. Plugin classes follow the
// convention of being the class-ified versions of these names suffixed with
// `Plugin`, ie. `FooBarPlugin`. Plugins should be under the
// `Silexhibit\Plugins\<PluginFolder>` namespace, ie. the classpath being like
// `Silexhibit\Plugins\FooBarPlugin\FooBarPlugin`.

// Conceptually, a plugin can be thought of as a component that's a third-party
// bundle of client-side and/or server-side functionality than can be integrated
// into any layer that implements a 'pluggable' interface, whether it be model,
// view, controller.

namespace Silexhibit;

use Silex\Application,
    Silex\ServiceProviderInterface;

use Doctrine\Common\Inflector\Inflector;

use Silexhibit\FactoryInterface;

class PluginFactoryServiceProvider implements ServiceProviderInterface, FactoryInterface
{
  // Core Configuration
  // ------------------

  // For the plugin factory to be able to register as a Silex service provider,
  // `service_name` must be set before calling the constructor. Conventionally,
  // it can be set by redeclaring the property. Conventionally, dot notation
  // should be used.
  protected $service_name = 'plugin.factory';
  // Convenience reference to the `app.opts` global.
  protected $app_options;
  // Convenience reference to the `app` global. Storing this makes it easier to
  // register plugins.
  protected $app;

  // Plugin Management
  // -----------------

  // `plugins` stores the plugin instances, with the keys being their name
  // (`id`). This internal structure makes the public API relatively
  // straightforward.
  protected $plugins;

  public function __construct()
  {
    $this->plugins = array();
  }

  // Silex Integration
  // -----------------

  // Conventionally, `register` should contain logic for setup and aliasing
  // related the dependencies from `app`.
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

  // Plugin Management
  // -----------------

  // `registerPlugin` will register the plugin only once upon creation. It will
  // always return the plugin.
  public function registerPlugin($name, $context)
  {
    $plugin = $this->getPlugin($name);
    if (!isset($plugin)) {
      // - Derive the class name conventionally from the name.
      $class_name = Inflector::classify($name).'Plugin';
      // - Derive the class path conventionally.
      $class_path = "Silexhibit\\Plugins\\$class_name\\$class_name";
      // - Create and store the instance.
      $plugin = $this->plugins[$name] = new $class_path($name);
      // - Initialize the plugin contextually and provide the opportunity to use
      //   existing service providers.
      $plugin->register($this->app, $context);
    }
    return $plugin;
  }

}
