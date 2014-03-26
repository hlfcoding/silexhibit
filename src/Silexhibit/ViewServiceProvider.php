<?php

namespace Silexhibit;

use Silex\Application,
    Silex\ServiceProviderInterface;

abstract class ViewServiceProvider implements ServiceProviderInterface
{
  public $templater;
  public $string_templater;
  public $component_templaters;

  protected $debug;

  protected $service_name;

  protected $controller;
  protected $url;
  protected $widgets;

  protected $content;
  protected $helpers;

  protected $app_options;
  protected $options;

  protected static $exposed_app_options = array(
    'app' => 'content',
    'metas',
    'head_links',
    'snippets',
    'log_level',
  );

  public function __construct(Controller $controller=null)
  {
    if ($controller) {
      $this->controller = $controller;
    }
    $this->content = array();
    $this->helpers = array();
  }

  public function register(Application $app)
  {
    $this->url = $app['url_generator'];

    $this->app_options = $app['app.opts'];
    $this->app_options['content']['full_url'] = $app['request']->getHttpHost().$app['request']->getRequestUri();
    if (isset($this->app_options['view'])) {
      $this->options = $this->app_options['view'];
    }
    $this->debug = $app['debug'];
    // Export.
    $app[$this->service_name] = $this;
  }
  public function boot(Application $app)
  {
  }

  public function render($data)
  {
    $this->content = array_merge($this->content, $this->transform($data));
    $this->setTemplaters();
    $this->exposeAppOptions();
    $this->content['js_templates'] = $this->getJSTemplates();
    $this->content['index'] = $this->getIndex();
    // Custom data.
    $this->content['app']['current_year'] = date('Y');
    $this->content['metas'][] = array(
      'name' => 'copyright',
      'content' => $this->string_templater->render(
        '(cc) {{copyright.start_year}}-{{current_year}} {{owner.name}}. {{copyright.meta}}',
        $this->content['app']
      ),
    );
    // /Custom data.
    $this->willRender();
    $this->controller->viewWillRender($this);
    $this->integrateAssetsAspect();
    if ($this->debug) {
      $this->integrateDebugAspect();
    }
    return $this->templater->render('index', array_merge($this->content, $this->helpers));
  }
  protected function setTemplaters()
  {
    if (!isset($this->templater)) {
      $this->templater = $this->controller->templater;
    }
    if (!isset($this->string_templater)) {
      $this->string_templater = $this->controller->string_templater;
    }
  }
  protected function exposeAppOptions()
  {
    foreach (self::$exposed_app_options as $new_key => $key) {
      if (!isset($this->app_options[$key])) {
        continue;
      }
      if (!is_string($new_key)) {
        $new_key = $key;
      }
      if (isset($this->content[$new_key])) {
        $this->content[$new_key] = array_merge_recursive(
          $this->content[$new_key], $this->app_options[$key]);
      } else {
        $this->content[$new_key] = $this->app_options[$key];
      }
    }
  }
  protected function integrateAssetsAspect()
  {
    $this->content = array_merge($this->content, $this->controller->asset_urls);
  }
  protected function integrateDebugAspect()
  {
    $this->content['debug_info'] = json_encode($this->content);
    //var_dump($this->content); die;
  }

  public function getJSTemplates($view_options=null, $template_loader=null, $template_path=null)
  {
    if (!isset($view_options)) {
      $view_options = $this->options;
    }
    if (!isset($view_options['js_templates'])) {
      return;
    }
    if (!isset($template_loader)) {
      $template_loader = $this->templater->getPartialsLoader();
    }
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

  // These get called after transform.
  abstract protected function getIndex($data=null);
  abstract protected function transformIndex($data);
  abstract protected function willRender();

  protected function transform($data)
  {
    $content = array();
    if ($this->debug) {
      $content['raw_data'] = $data;
    }
    return $content;
  }

  protected function generatePreviewText($content_html, $max_length=240) {
    $text = strip_tags($content_html, '<p><div>');
    $has_p = strpos($text, '<p>') !== false;
    $end = $has_p ? '</p>' : '</div>';
    $end = strpos($text, $end) + strlen($end);
    $text = substr($text, 0, $end);
    $text = strip_tags($text);
    while (strlen($text) > $max_length) {
      $end = strrpos($text, '.', -2);
      $text = substr($text, 0, ($end + 1));
    }
    return $text;
  }

}
