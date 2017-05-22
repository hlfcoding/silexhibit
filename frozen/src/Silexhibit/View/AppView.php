<?php

namespace Silexhibit\View;

use Silex\Application;

use Doctrine\Common\Inflector\Inflector;

use Silexhibit\ViewServiceProvider;

class AppView extends ViewServiceProvider
{
  protected $service_name = 'app.view';

  protected function getIndex($data=null)
  {
    return null;
  }
  protected function transformIndex($data)
  {
    return $data;
  }
  protected function willRender()
  {
    $this->content['app']['sections'] = array_map(function ($section) {
      $section['slug'] = Inflector::pluralize($section['name']);
      return $section;
    }, $this->content['app']['sections']);
    $this->content['app']['exhibit'] = array();
    foreach (array(
      'accepted_image_mimes',
      'formats',
      'max_image_sizes',
      'max_upload_size',
      'thumbnail_sizes',
    ) as $key) {
      $this->content['app']['exhibit'][$key] = $this->controller->provideData("exhibit_$key");
    }
    $this->content['app'] = array_merge($this->content['app'], $this->controller->getURLs());
    $this->content['app']['current_section'] = $this->content['app']['sections'][0];
    $app_json = json_encode($this->content['app']);
    $this->content['snippets'][] = "Silexhibit.jsonContent.app = $app_json;";
  }
}
