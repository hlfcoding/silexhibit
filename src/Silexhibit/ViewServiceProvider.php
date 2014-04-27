<?php

// Silexhibit View
// ===============
// This is the base view class. It can be used as a Silex service provider. Its
// main responsibilities include preparing the content and template context
// before rendering, supporting and managing components, if any. Preparing the
// content includes also integrating any content supplied through configuration.
// Preparing the template context also includes adding any client-side
// templates.

namespace Silexhibit;

use Silex\Application,
    Silex\ServiceProviderInterface;

// Required Silex application globals, helpers, and services:
// `debug`, `app.opts`, `url_generator`.

abstract class ViewServiceProvider implements ServiceProviderInterface
{
  // Core Flags
  // ----------
  
  // Internal flag derived from the `debug` Silex global. This can be modified
  // independently by a subclass for Silexhibit application-specific debugging.
  protected $debug;

  // Templaters
  // ----------
  // These templater references are conventionally from `controller` and set by
  // `updateFromController`. The view only uses them and doesn't manage them.
  public $templater;
  public $string_templater;
  public $component_templaters;

  // Core Configuration
  // ------------------

  // For the view to be able to register as a Silex service provider,
  // `service_name` must be set before calling the constructor. Conventionally,
  // it can be set by redeclaring the property. Conventionally, dot notation
  // should be used.
  protected $service_name;
  // Convenience reference to the `app.opts` global.
  protected $app_options;
  // Convenience reference to the `view` configuration option group in
  // `app_options`.
  protected $options;

  // Bundled Services
  // ----------------

  // Conventionally, the view should know about its `controller`. This tight
  // coupling means the view should only have one controller, which acts as its
  // delegate and will be notified of its actions as well as provide it any
  // needed data.
  protected $controller;
  // `url` is a convenience reference to the `url_generator` service.
  protected $url;

  // Content Management
  // ------------------
  
  // `content` is the central source of truth for the content presented by the
  // view and sent to the template. Conventionally, this store will be
  // transformed and expanded throughout the process of pre-rendering by the
  // view as well as any components with the view acting on their behalf.
  protected $content;
  // `helpers` is a tentative store for helper functions that also gets sent to
  // the template.
  protected $helpers;
  // `exposed_app_options` is the static map of what application configuration
  // options to pass to `content`. If an entry has a specified key, that key
  // will be the entry's new name when it gets copied.
  protected static $exposed_app_options = array(
    'app' => 'content',
    'metas',
    'head_links',
    'snippets',
    'log_level',
  );

  // Constructor
  // -----------
  // Conventionally, `controller` is required for the view to fully function.
  public function __construct(Controller $controller=null)
  {
    // - Store reference to given `controller`.
    if ($controller) {
      $this->controller = $controller;
    }
    // - Initialize content management.
    $this->content = array();
    $this->helpers = array();
  }

  // Silex Integration
  // -----------------
  
  // Conventionally, `register` should contain logic for setup and aliasing
  // related the dependencies from `app`.
  public function register(Application $app)
  {
    // - Alias services and globals.
    $this->url = $app['url_generator'];
    $this->debug = $app['debug'];
    // - Setup options. On occasion for certain URLs, the template may need to
    //   render the app's `full_url`.
    $this->app_options = $app['app.opts'];
    $this->app_options['content']['full_url'] = $app['request']->getHttpHost().$app['request']->getRequestUri();
    if (isset($this->app_options['view'])) {
      $this->options = $this->app_options['view'];
    }
    // - Publish self as service.
    $app[$this->service_name] = $this;
  }
  public function boot(Application $app)
  {
  }

  // Controller-Driven API
  // ---------------------

  // `render` is the view's main public API. It calls the other render-related
  // subroutines. It also can add or transform any custom content. Tentatively,
  // it renders the `main` root template, which has slots for custom content
  // partials that can subsequently contain more partials slots.
  public function render($data)
  {
    // - First, run `transform` on `data` and update `content` by merging it
    //   with `data`.
    $this->content = array_merge($this->content, $this->transform($data));
    // - Next, run the `exposeAppOptions` subroutine.
    $this->exposeAppOptions();
    // - Next, setup specific content with the results from respective
    //   subroutines.
    $this->content['js_templates'] = $this->getJSTemplates();
    $this->content['index'] = $this->getIndex();
    // - Next, add any custom data where the data is dynamic, or separate
    //   partials would be too awkward to implement.
    $this->content['app']['current_year'] = date('Y');
    $this->content['metas'][] = array(
      'name' => 'copyright',
      'content' => $this->string_templater->render(
        '(cc) {{copyright.start_year}}-{{current_year}} {{owner.name}}. {{copyright.meta}}',
        $this->content['app']
      ),
    );
    // - Next, call the will-render hooks.
    $this->willRender();
    $this->controller->viewWillRender($this);
    // - Next, setup `asset_urls` and any other asset-related content.
    $this->integrateAssetsAspect();
    if ($this->debug) {
      // - Next, setup `debug_info` and any other debug-related content.
      $this->integrateDebugAspect();
    }
    // - Finally, render the template with the final context.
    return $this->templater->render('main', array_merge($this->content, $this->helpers));
  }
  // `updateFromController` copies by reference any controller properties needed
  // by the view to the view, and is called automatically by the controller when
  // it registers the view. By default, only the templaters are copied. This
  // method can be extended to copy more properties.
  public function updateFromController()
  {
    if (!isset($this->templater)) {
      $this->templater = $this->controller->templater;
    }
    if (!isset($this->string_templater)) {
      $this->string_templater = $this->controller->string_templater;
    }
    if (!isset($this->component_templaters)) {
      $this->component_templaters = $this->controller->component_templaters;
    }
  }

  // Content Preparation Subroutines
  // -------------------------------
  
  // `exposeAppOptions` extends `content` with selected configuration options
  // per `exposed_app_options`.
  protected function exposeAppOptions()
  {
    foreach (self::$exposed_app_options as $new_key => $key) {
      // - Guard against app configuration options being omitted (ie. disabled)
      //   on purpose.
      if (!isset($this->app_options[$key])) {
        continue;
      }
      // - `new_key` is the same by default, unless a custom one is provided.
      if (!is_string($new_key)) {
        $new_key = $key;
      }
      // - Merge if needed on setting.
      if (isset($this->content[$new_key])) {
        $this->content[$new_key] = array_merge_recursive(
          $this->content[$new_key], $this->app_options[$key]);
      } else {
        $this->content[$new_key] = $this->app_options[$key];
      }
    }
  }
  // `integrateAssetsAspect` by default extends `content` with `controller`'s
  // `asset_urls`. It can be extended for further extension by subclasses.
  protected function integrateAssetsAspect()
  {
    $this->content = array_merge($this->content, $this->controller->asset_urls);
  }
  // `integrateAssetsAspect` by default extends `content` with `debug_info`,
  // which just the json-encoded `content`. It can be extended for further
  // extension by subclasses.
  protected function integrateDebugAspect()
  {
    $this->content['debug_info'] = json_encode($this->content);
    //var_dump($this->content); die;
  }

  // Content Preparation API
  // -----------------------

  // `getJSTemplates` solves the chicken-or-egg problem of attempting to embed
  // Mustache partials for client-side use. If disabling tags for MustachePHP,
  // the client-side template markup won't be run through the templater, but the
  // containing partial itself won't be loaded either. This data generator can
  // run conventionally and without parameters, where it takes `options` and
  // transforms the list of extension-less filenames (which can be wrapped in
  // hash and tied to the key `id`) into objects with `id` and `template` --
  // markup from partials loaded by the main dedicated `templater`.
  public function getJSTemplates($view_options=null, $template_loader=null, $template_path=null)
  {
    // - Use default view options if needed. The options key must be
    //   `js_templates`.
    if (!isset($view_options)) {
      $view_options = $this->options;
    }
    if (!isset($view_options['js_templates'])) {
      return;
    }
    // - Use default template loader if needed.
    if (!isset($template_loader)) {
      $template_loader = $this->templater->getPartialsLoader();
    }
    // - For each template, normalize its `info` into a hash and set its value
    //   for `template`. Return the final list of template info objects.
    return array_map(function ($info) use ($template_loader, $template_path) {
      if (!is_array($info)) {
        $id = $info;
        $info = array('id' => $id);
      } else {
        $id = $info['id'];
      }
      $info['template'] = $template_loader->load(
        isset($template_path) ? "$template_path/$id" : $id
      );
      return $info;
    }, $view_options['js_templates']);
  }

  // `getIndex` should return a content hash that can directly be attached to
  // `content` as `index`.
  abstract protected function getIndex($data=null);
  // `transformIndex` should return a transformed content hash that can be
  // eventually returned by `getIndex`. Conventionally, `getIndex` calls
  // `transformIndex` as a subroutine.
  abstract protected function transformIndex($data);
  // `willRender` is a hook for subclasses to extend `content` and run any
  // components.
  abstract protected function willRender();

  // `transform` by default hides given `data` under the `raw_data` key and
  // keeps the `content` initially empty otherwise. It should be extended for
  // further extension by subclasses.
  protected function transform($data)
  {
    if ($this->debug) {
      $content['raw_data'] = $data;
    }
    return $content;
  }

  // Helpers
  // -------

  // `generatePreviewText` helps generate a tag-less, sanely cropped description
  // of given `content_html`.
  protected function generatePreviewText($content_html, $max_length=240)
  {
    // - First strip the inline tags (not `p` or `div`).
    $text = strip_tags($content_html, '<p><div>');
    // - Next isolate our `text` block by finding the `end` based on end tag.
    $has_p = strpos($text, '<p>') !== false;
    $end = $has_p ? '</p>' : '</div>';
    $end = strpos($text, $end) + strlen($end);
    // - Next crop our `text` based on `end` and strip the remaining tags.
    $text = substr($text, 0, $end);
    $text = strip_tags($text);
    // - Finally, if we're over `max_length` crop to the closest full sentence.
    while (strlen($text) > $max_length) {
      $end = strrpos($text, '.', -2);
      $text = substr($text, 0, ($end + 1));
    }
    return $text;
  }

}
