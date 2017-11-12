<?php

namespace Silexhibit;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

interface ThemeServiceInterface extends ServiceProviderInterface {

  public function renderPost(array $post, Container $app);
  public function renderIndex(array $index, int $type, Container $app);
  public function wrapTemplateData(array $data, Container $app);

}
