<?php

namespace Silexhibit;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataAdapterServiceProvider implements ServiceProviderInterface {

  public function register(Container $app) {
    $app['adapter'] = $this;
  }

  public function conventionalExhibit($input, $options = array()) {
    $output = array();
    $media_map = array(
      'kb' => 'file_kb',
      'mime' => 'file_mime',
      'obj_type' => 'object',
      'ref_id' => 'object_id',
      'udate' => 'updated_at',
      'uploaded' => 'uploaded_at',
      'x' => 'width',
      'y' => 'height',
    );
    $media_options = array('prefix_to_remove' => 'media_');
    $output['exhibit'] = array_map(function($media) use ($media_map, $media_options) {
      return $this->rename($media, $media_map, $media_options);
    }, $input['exhibit']);
    unset($input['exhibit']);

    $site_map = array(
      'ibot' => 'post_nav_text',
      'itop' => 'pre_nav_text',
      'mode' => 'is_advanced_mode',
      'org' => 'index_type',
    );
    $site_options = array(
      'flag_keys' => array('is_advanced_mode'),
      'prefix_to_remove' => 'obj_',
    );
    $site_data = array_filter($input, function($key) use ($site_options) {
      return strpos($key, $site_options['prefix_to_remove']) === 0;
    }, ARRAY_FILTER_USE_KEY);
    $output['site'] = $this->rename($site_data, $site_map, $site_options);
    foreach (array_keys($site_data) as $key) { unset($input[$key]); }

    $map = array(
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
    );
    $options['flag_keys'] = array(
      'is_current', 'is_hidden', 'should_break', 'should_tile_background',
    );
    $options['skip_keys'] = array(
      /* Deprecated: */ 'color', 'creator',
      /* Redundant: */ 'ord',
      /* System: */ 'page_cache', 'process', 'report',
    );
    $output = array_merge($output, $this->rename($input, $map, $options));
    ksort($output);
    return $output;
  }

  public function conventionalExhibitIndex($input) {
    $map = array(
      'section' => array('section_name', 'section.folder_name'),
      'secid' => 'section.id',
      'sec_desc' => 'section.name',
      'sec_disp' => 'should_display_name',
    );
    return array_map(function ($item) use ($map) {
      return $this->rename($item, $map);
    }, $input);
  }

  protected function rename($input, $map, $options = array()) {
    /* TODO
    if ($options['reverse']) { $map = array_flip($map); }
    $skip = $options['reverse'] ? array('section', 'section_name', 'site') : $options['skip_keys'];
    $value = $options['reverse'] ? (int)$value : !!$value;
    */
    $output = array();
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
      if (!is_array($new_keys)) { $new_keys = array($new_keys); }
      foreach ($new_keys as $new_key) {
        $parts = explode('.', $new_key);
        $data = &$output;
        while (count($parts) > 1) {
          $child = array_shift($parts);
          if (!isset($data[$child])) { $data[$child] = array(); }
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
