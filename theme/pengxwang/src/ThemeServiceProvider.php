<?php

namespace Silexhibit;

use Pimple\Container;
use Silexhibit\ThemeServiceInterface;

class ThemeServiceProvider implements ThemeServiceInterface {

  protected $post;

  public function register(Container $app) {
    $app['theme'] = $this;
  }

  public function renderPost(array $post, Container $app) {
    $this->post = $post;
    $this->post['exhibit_count'] = count($this->post['exhibit']);
    return $app['mustache']->render($post['format'], $post);
  }

  public function renderIndex(array $index, int $type, Container $app) {
    if (isset($index['sections'])) {
      $detail_id = $this->post['id'];
      $index['sections'] = array_map(function ($s) use ($detail_id) {
        $s['posts'] = array_map(function ($p) use ($detail_id) {
          $post = $p;
          $post['preview_html'] = $p['content']; unset($post['content']);
          $post['preview_text'] = $this->generatePreviewText($p['content']);
          $post['is_active'] = $p['id'] === $detail_id;
          return $post;
        }, $s['posts']);
        return $s;
      }, $index['sections']);
    }
    return $index;
  }

  public function wrapTemplateData(array $data, Container $app) {
    $config = require $app['root'].'config/theme/common.php';
    return array_merge_recursive($data, [
      'config' => ['theme' => $config],
      'debug_info' => $app['debug'] ? json_encode($app['config'], JSON_PRETTY_PRINT) : null,
    ]);
  }

  protected function generatePreviewText(string $html, int $max_length = 240) {
    // - First strip the inline tags (not `p` or `div`).
    $text = strip_tags($html, '<p><div>');
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
    return trim($text);
  }

}
