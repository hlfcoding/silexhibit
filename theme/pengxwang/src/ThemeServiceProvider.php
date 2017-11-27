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
    if (isset($this->post['exhibit'])) {
      $this->post['exhibit_count'] = count($this->post['exhibit']);
    }
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
      'debug_info' => $app['debug'] ? json_encode(
        array_merge($data, array('post' => $this->post)),
      JSON_PRETTY_PRINT) : null,
    ]);
  }

  protected function generatePreviewText(string $html, int $max_length = 280) {
    // Strip non-content tags, then replace content tags.
    $html = preg_replace("/<script(.*?)>(.*?)<\\/script>/is", '', $html);
    $html = html_entity_decode(strip_tags($html, '<p><li><h3><dd>'));
    $text = preg_replace("/<\\/?(p|li|h3|dd)(.*?)>/is", '', $html);
    // If needed, crop to the closest full sentence.
    while (strlen($text) > $max_length) {
      $end = strrpos($text, '. ', -2);
      if ($end === 0 || $end === false) { break; }
      $text = substr($text, 0, ($end + 1));
    }
    return trim($text);
  }

}
