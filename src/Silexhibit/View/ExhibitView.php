<?php

namespace Silexhibit\View;

use Silex\Application;

use Symfony\Component\Routing\Generator\UrlGenerator;

use Silexhibit\ViewServiceProvider,
    Silexhibit\Helper\Security,
    Silexhibit\Plugin\PluggableViewInterface,
    Silexhibit\Plugin\PluggableViewTrait,
    Silexhibit\Traits\ExhibitTransformerTrait;

class ExhibitView extends ViewServiceProvider implements PluggableViewInterface
{
  use ExhibitTransformerTrait;
  use PluggableViewTrait;

  protected $service_name = 'exhibit.view';

  protected $widgets;

  public function __construct($controller=null)
  {
    parent::__construct($controller);
  }

  public function register(Application $app)
  {
    parent::register($app);
    $this->widgets = $app['exhibit.factory'];
    $this->plugins = $app['plugin.factory'];
  }

  protected function getIndex($data=null)
  {
    $type = $this->content['site']['index_type'];
    if (!$data) {
      $model = $this->controller->modelForView($this);
      $data = $this->controller->provideData('exhibit_index', array('type' => $type));
    }
    $content = $this->transformIndex($data);
    // Restructure.
    $ordered = array();
    switch ($type) {
      case $model::INDEX_CHRONOLOGICAL:
        // TODO: Stub.
        break;
      case $model::INDEX_SECTIONAL:
        foreach ($content as $item) {
          // Sort by name since id affects order.
          $group =& $ordered[$item['section']['folder_name']];
          if (!isset($group)) {
            $group = $item['section'];
            $group['items'] = array();
          }
          unset($item['section']);
          $group['items'][] = $item;
        }
        $key_name = 'sections';
        break;
      default: break;
    }
    foreach ($ordered as &$group) {
      $group['item_count'] = count($group['items']);
    }
    return array(
      'type' => $model::$type_names[$type],
      // Only send values, since only lists are allowed.
      $key_name => array_values($ordered),
    );
  }

  public function transform($data)
  {
    $content = parent::transform($data);
    $this->conventionallyTransform($data, array(), $content);
    // Additional conversion.
    if (isset($this->controller)) {
      $content['status'] = $this->controller->provideData('exhibit_status_names', array(
        'status' => $content['status']
      ));
    }
    // Additional vars.
    if (isset($this->url)) {
      $content['site']['url'] = $this->url->generate('homepage', array(), true);
    }
    return $content;
  }

  protected function transformIndex($data)
  {
    $detail_id = $this->content['id'];
    $base_url = $this->content['site']['url'];
    // Since this is a small set of keys and in need of thorough renaming.
    return array_map(function ($item, $original_item) use ($base_url, $detail_id) {
      return array_merge($item, array(
        'url' => (rtrim($base_url, '/').$original_item['url']),
        'preview_html' => $original_item['content'],
        'preview_text' => $this->generatePreviewText($original_item['content']),
        'is_active' => $original_item['id'] === $detail_id,
      ));
    }, $this->conventionallyTransformIndex($data), $data);
  }

  protected function willRender()
  {
    $email =& $this->content['site']['owner']['email']; // Alias.
    Security::antispambot($email);
    $widget = $this->widgets->generateWidget($this->content);
    $content = $this->content['exhibit_content'] = $widget['content'];
    $this->content['exhibit_html'] = $this->component_templaters['exhibit']->render(
      "{$content['exhibit_name']}/main", $content);
    // Plugins.
    $this->runPlugins();
  }

  // PluggableViewInterface
  // ----------------------

  public function getPluggableHtml()
  {
    return $this->content['exhibit_html'];
  }
  public function setPluggableHtml($html)
  {
    return $this->content['exhibit_html'] = $html;
  }
  public function getPluginTemplater()
  {
    return $this->component_templaters['plugin'];
  }

  public function addAssetsForPlugin($name)
  {}

  public function addPluginSnippet($js)
  {
    $this->content['snippets'][] = $js;
  }

}
