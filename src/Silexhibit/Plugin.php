<?php

// Silexhibit Plugin
// =================
// This is the base plugin class. Its main responsibilities include getting the
// plugin's configuration option group, running according to its context, and
// providing a basic means conventional means of rendering and modifying its
// view's content. See `PluginFactoryServiceProvider` for a deeper discussion
// about the concepts behind plugins. See existing plugins for sample directory
// structures, which are conventional.

namespace Silexhibit;

use Silex\Application;

use Silexhibit\Plugin\PluggableViewInterface;

abstract class Plugin
{
  // Each plugin must be constructed with a `name` by its factory. The name
  // should be unique within the factory.
  public $name;
  // Convenience reference to the `plugin.<name>` configuration global.
  public $config;
  // `context` decides how a plugin will `register` and `run`, as well as
  // perhaps other things depending on the subclass. For example, if the context
  // is a implementation of `PluggableViewInterface`, ie. a pluggable view, the
  // plugin will `render` itself on `run`.
  protected $context;

  // Constructor
  // -----------
  // Conventionally, `name` is required from the plugin factory.
  public function __construct($name)
  {
    $this->name = $name;
  }

  // Core
  // ----

  // `register` initializes the plugin given the `app` and a certain `context`.
  // Conventionally, a plugin's `name` should be used as its identifier by other
  // plugin source. See `PluginFactoryServiceProvider` for details on naming
  // conventions.
  public function register(Application $app, $context)
  {
    // - Store the default context.
    $this->context = $context;
    // - Get the configuration option group.
    if (isset($app["plugin.{$this->name}"])) {
      $this->config = $app["plugin.{$this->name}"];
    }
  }
  // `run` should run the plugin given its `context` (whether default or not)
  // and an options bag. It should always return whether or not the plugin
  // actually ran.
  public function run($opts, $context=null)
  {
    // - Allow for a custom context.
    $context = isset($context) ?: $this->context;
    // - Render if needed.
    if ($context instanceof PluggableViewInterface) {
      $view = $context;
      return $this->render($opts, $view);
    }
    return false;
  }
  // The `render` subroutine simply renders the options bag (as `base_content`)
  // by integrating with the `PluggableViewInterface` implementer to
  // `getTemplater` and `getJSTemplates` as needed. It is up to the plugin
  // subclass to `provideContent`. The plugin `templater`, which is
  // conventionally shared between all the view's plugins and just the
  // controller's main plugin templater, will render the final `content` with
  // plugin's `main` template.
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
  // `provideContent` should be implemented by the plugin subclass to return
  // plugin-specific content to be rendered into the main plugin template.
  abstract protected function provideContent(PluggableViewInterface $view);
}
