<?php

namespace Silexhibit;

use Pimple\Container;
use Silexhibit\ThemeServiceInterface;

class ThemeServiceProvider implements ThemeServiceInterface {

  public function register(Container $app) {
    $app['theme'] = $this;
  }

  public function renderPost(array $post, Container $app) {
    return json_encode($post, JSON_PRETTY_PRINT);
  }

  public function renderIndex(array $index, int $type, Container $app) {
    return json_encode($index, JSON_PRETTY_PRINT);
  }

  public function wrapTemplateData(array $data, Container $app) {
    return array_merge($data, [
      'debug_info' => json_encode($app['config'], JSON_PRETTY_PRINT),
    ]);
  }

}
