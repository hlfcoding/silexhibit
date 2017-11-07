<?php

namespace Silexhibit;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ThemeServiceProvider implements ServiceProviderInterface {

  public function register(Container $app) {
    $app['theme'] = $this;
  }

  public function renderExhibit(array $exhibit, Container $app) {
    return json_encode($exhibit, JSON_PRETTY_PRINT);
  }

  public function renderIndex(array $index, int $type, Container $app) {
    return json_encode($index, JSON_PRETTY_PRINT);
  }

  public function wrapContent(array $content, Container $app) {
    return array_merge($content, array(
      'debug_info' => json_encode($app['config'], JSON_PRETTY_PRINT),
    ));
  }

}
