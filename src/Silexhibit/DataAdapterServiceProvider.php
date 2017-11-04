<?php

namespace Silexhibit;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataAdapterServiceProvider implements ServiceProviderInterface {

  public function register(Container $app) {
    $app['adapter'] = $this;
  }

  public function conventionalExhibit($input, $options = array(), &$output = array()) {
    $options = array_merge(array(
      'reverse' => false, 'omit_skipped' => true, 'omit_extra_keys' => true
    ), $options);
    $site_prefix = 'obj_';
    $rename = array(
      'bgimg' => 'background_image',
      'header' => 'header_html',
      'images' => 'max_image_size',
      'thumbs' => 'thumbnail_size',
      'pdate' => 'posted_at',
      'udate' => 'updated_at',
      'itop' => 'pre_nav_text',
      'ibot' => 'post_nav_text',
      'org' => 'index_type',
    );
    if ($options['reverse']) {
      $rename = array_flip($rename);
    }
    $rename = array($rename, array_keys($rename));
    $rename_and_convert = array(
      'break' => 'should_break',
      'current' => 'is_current',
      'hidden' => 'is_hidden',
      'tiling' => 'should_tile_background',
      'mode' => 'is_advanced_mode',
    );
    if ($options['reverse']) {
      $rename_and_convert = array_flip($rename_and_convert);
    }
    $rename_and_convert = array($rename_and_convert, array_keys($rename_and_convert));
    $skip = $options['reverse'] ? array(
      'section', 'section_name', 'site',
    ) : array(
      // Deprecated.
      'color', 'creator',
      // Redundant.
      'ord',
      // System.
      'page_cache', 'process', 'report',
    );
    if (!$options['reverse']) {
      $output['site'] = array();
    }
    foreach ($input as $key => $value) {
      if ($options['omit_skipped'] && in_array($key, $skip)) { continue; }
      $is_site_key = !$options['reverse'] && strpos($key, $site_prefix) === 0;
      $new_key = $is_site_key ? substr($key, strlen($site_prefix)) : $key;
      $new_value = $value;
      foreach (array($rename, $rename_and_convert) as list($map, $map_keys)) {
        if (!in_array($new_key, $map_keys)) { continue; }
        $new_key = $map[$new_key];
        if ($map === $rename_and_convert[0]) {
          $new_value = $options['reverse'] ? (int)$new_value : !!$new_value;
        }
        break;
      }
      if ($new_key === 'exhibit') {
        $new_value = array_map(function($media) {
          return $this->conventionalExhibitMedia($media);
        }, $new_value);
      }
      if ($is_site_key) {
        $output['site'][$new_key] = $new_value;
      } else {
        $output[$new_key] = $new_value;
      }
    }
    ksort($output);
    return $output;
  }

  protected function conventionalExhibitMedia($input, &$output = array()) {
    $prefix = 'media_';
    $rename = array(
      'kb' => 'file_kb',
      'mime' => 'file_mime',
      'obj_type' => 'object',
      'ref_id' => 'object_id',
      'udate' => 'updated_at',
      'uploaded' => 'uploaded_at',
      'x' => 'width',
      'y' => 'height',
    );
    $rename = array($rename, array_keys($rename));
    foreach ($input as $key => $value) {
      $new_key = (strpos($key, $prefix) === 0) ? substr($key, strlen($prefix)) : $key;
      $new_value = $value;
      list($map, $map_keys) = $rename;
      if (in_array($new_key, $map_keys)) {
        $new_key = $map[$new_key];
      }
      $output[$new_key] = $new_value;
    }
    ksort($output);
    return $output;
  }

}
