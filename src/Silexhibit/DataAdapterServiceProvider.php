<?php

namespace Silexhibit;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Silexhibit\INDEX_CHRONOLOGICAL;
use Silexhibit\INDEX_SECTIONAL;

class DataAdapterServiceProvider implements ServiceProviderInterface {

  public function register(Container $app) {
    $app['adapter'] = $this;
  }

  public function conventionalPost(array $input, array $options = []) {
    $output = [];
    if (isset($input['exhibit'])) {
      $media_map = [
        'kb' => 'file_kb',
        'mime' => 'file_mime',
        'obj_type' => 'object',
        'ref_id' => 'object_id',
        'udate' => 'updated_at',
        'uploaded' => 'uploaded_at',
        'x' => 'width',
        'y' => 'height',
      ];
      $media_options = ['prefix_to_remove' => 'media_'];
      $output['exhibit'] = array_map(function($media) use ($media_map, $media_options) {
        return $this->rename($media, $media_map, $media_options);
      }, $input['exhibit']);
      $output['exhibit_count'] = count($output['exhibit']);
      unset($input['exhibit']);
    }

    $site_map = [
      'ibot' => 'post_nav_text',
      'itop' => 'pre_nav_text',
      'mode' => 'is_advanced_mode',
      'org' => 'index_type',
    ];
    $site_options = [
      'flag_keys' => ['is_advanced_mode'],
      'prefix_to_remove' => 'obj_',
    ];
    $site_data = array_filter($input, function($key) use ($site_options) {
      return strpos($key, $site_options['prefix_to_remove']) === 0;
    }, ARRAY_FILTER_USE_KEY);
    $output['site'] = $this->rename($site_data, $site_map, $site_options);
    foreach (array_keys($site_data) as $key) { unset($input[$key]); }

    $map = [
      'bgimg' => 'background_image',
      'break' => 'should_break',
      'current' => 'is_current',
      'header' => 'header_html',
      'hidden' => 'is_hidden',
      'images' => 'max_image_size',
      'pdate' => 'posted_at',
      'thumbs' => 'thumbnail_size',
      'tiling' => 'should_tile_background',
      'udate' => 'updated_at',
    ];
    $options['flag_keys'] = [
      'is_current', 'is_hidden', 'should_break', 'should_tile_background',
    ];
    $options['skip_keys'] = [
      /* Deprecated: */ 'color', 'creator',
      /* Redundant: */ 'ord',
      /* System: */ 'page_cache', 'process', 'report',
    ];
    $output = array_merge($output, $this->rename($input, $map, $options));
    ksort($output);
    return $output;
  }

  static $index_type_names = [
    INDEX_CHRONOLOGICAL => 'chronological',
    INDEX_SECTIONAL => 'sectional',
  ];

  public function conventionalIndex(array $input, array $options = []) {
    $map = [
      'section' => ['section_name', 'section.folder_name'],
      'secid' => 'section.id',
      'sec_desc' => 'section.name',
      'sec_disp' => 'section.should_display_name',
    ];
    $output = array_map(function($post) use ($map) {
      return $this->rename($post, $map);
    }, $input);
    $ordered = [];
    $index_key;
    switch ($options['type']) {
      case INDEX_CHRONOLOGICAL: break;
      case INDEX_SECTIONAL:
        $index_key = 'sections';
        foreach ($output as $post) {
          $key = $post['section']['folder_name'];
          if (!isset($ordered[$key])) {
            $ordered[$key] = $post['section'];
            $ordered[$key]['posts'] = [];
          }
          unset($post['section']);
          $ordered[$key]['posts'][] = $post;
        }
        break;
      default: break;
    }
    foreach ($ordered as &$group) {
      $group['post_count'] = count($group['posts']);
    }
    $output = [
      'type' => self::$index_type_names[$options['type']],
      $index_key => array_values($ordered), // Only lists are allowed.
    ];
    return $output;
  }

  protected function rename(array $input, array $map, array $options = []) {
    /* TODO
    if ($options['reverse']) { $map = array_flip($map); }
    $skip = $options['reverse'] ? ['section', 'section_name', 'site'] : $options['skip_keys'];
    $value = $options['reverse'] ? (int)$value : !!$value;
    */
    $output = [];
    foreach ($input as $key => $value) {
      if (isset($options['prefix_to_remove'])) {
        $prefix = $options['prefix_to_remove'];
        if (strpos($key, $prefix) === 0) {
          $key = substr($key, strlen($prefix));
        }
      }
      if (!isset($map[$key]) || (
        isset($options['skip_keys']) && in_array($key, $options['skip_keys'])
      )) {
        $output[$key] = $value;
        continue;
      }
      $new_keys = $map[$key];
      if (!is_array($new_keys)) { $new_keys = [$new_keys]; }
      foreach ($new_keys as $new_key) {
        $parts = explode('.', $new_key);
        $data = &$output;
        while (count($parts) > 1) {
          $child = array_shift($parts);
          if (!isset($data[$child])) { $data[$child] = []; }
          $data = &$data[$child];
        }
        $new_key = $parts[0];
        if (isset($options['flag_keys']) && in_array($new_key, $options['flag_keys'])) {
          $value = !!$value;
        }
        $data[$new_key] = $value;
        if ($data !== $output) { ksort($data); }
      }
    }
    ksort($output);
    return $output;
  }

}
