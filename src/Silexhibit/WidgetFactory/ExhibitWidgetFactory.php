<?php

namespace Silexhibit\WidgetFactory;

use Silex\Application;

use Silexhibit\WidgetFactoryServiceProvider;

class ExhibitWidgetFactory extends WidgetFactoryServiceProvider
{
  public $service_name = 'exhibit.factory';

  public function register(Application $app)
  {
    parent::register($app);
  }

  public function generateWidget($data)
  {
    $name = isset($data['format']) ?
      $data['format'] : 'blank';
    $widget = $this->getWidget($name);
    if (isset($widget)) {
      return $widget;
    }
    $content = array();
    $widget = array(
      'name' => $name,
      'content' => &$content,
      'config' => $this->app["exhibit.$name"],
    );
    $this->registerWidget($name, $widget);
    $content['exhibit_name'] = $name;
    $this->addBaseExhibitContent($data, $content);
    switch ($name) {
      case 'slideshow':
        break;
      default: break;
    }
    return $widget;
  }

  protected function addBaseExhibitContent($data, &$content)
  {
    $content['html'] = $data['content'];
    if (isset($data['exhibit'])) {
      $content['cards'] = $data['exhibit'];
      $content['card_count'] = count($content['cards']);
      foreach ($content['cards'] as &$card) {
        if (isset($card['media_file'])) {
          $card['media_url'] = "{$this->app_options['image_baseurl']}/exhibit/{$card['media_file']}";
        }
      }
    }
  }
}
