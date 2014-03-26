<?php

namespace Silexhibit\Traits;

trait ExhibitTransformerTrait
{
  protected static $transform_skipped_keys = array(
    // Deprecated.
    'color',
    'creator',
    // Redundant.
    'ord',
    // System.
    'page_cache',
    'process',
    'report',
  );
  protected static $transform_renamed_keys = array(
    'bgimg' => 'background_image',
    'header' => 'header_html',
    'images' => 'max_image_size',
    'thumbs' => 'thumbnail_size',
    'pdate' => 'time_posted',
    'udate' => 'time_updated',
    'obj_itop' => 'pre_nav_text',
    'obj_ibot' => 'post_nav_text',
    'obj_org' => 'index_type',
  );
  // Conversion, plus likely rename.
  protected static $transform_bool_keys = array(
    'break' => 'should_break',
    'current' => 'is_current',
    'hidden' => 'is_hidden',
    'tiling' => 'should_tile_background',
    'obj_mode' => 'is_advanced_mode',
  );
  protected static $transform_extra_keys = array(
    'section',
    'section_name',
    'site',
  );
  protected static $transform_default_options = array(
    'reverse' => false,
    'omit_skipped' => true,
    'omit_extra_keys' => true,
  );

  private static $transform_site_prefix = 'obj_';

  public function allTransformedKeys()
  {
    return array_merge(self::$transform_renamed_keys, self::$transform_bool_keys);
  }

  public function conventionallyTransform($input, $options=array(), &$output=array())
  {
    $options = array_merge(self::$transform_default_options, $options);
    $site_px_len = strlen(self::$transform_site_prefix);
    $renamed_keys = self::$transform_renamed_keys;
    $bool_keys = self::$transform_bool_keys;
    if ($options['reverse']) {
      $renamed_keys = array_flip($renamed_keys);
      $bool_keys = array_flip($bool_keys);
    } else {
      $output['site'] = array();
    }
    foreach ($input as $key => $value) {
      // Main loop:
      if ((!$options['reverse'] && $options['omit_skipped'] &&
          in_array($key, self::$transform_skipped_keys)) ||
        ($options['reverse'] &&
          in_array($key, self::$transform_extra_keys))
      ) {
        continue;
      }
      $is_site_key = !$options['reverse'] && strpos($key, self::$transform_site_prefix) === 0;
      // Rename as needed.
      $mapped = false;
      foreach (array($renamed_keys, $bool_keys) as $map) {
        if (in_array($key, array_keys($map))) {
          $mapped = true;
          $new_key = $map[$key];
          if ($map === $bool_keys) {
            $value = $options['reverse'] ? (int)$value : !!$value;
          }
          if ($is_site_key) {
            // Restructure with rename.
            $output['site'][$new_key] = $value;
          } else {
            $output[$new_key] = $value;
          }
          break;
        }
      }
      if (!$mapped) {
        if ($is_site_key) {
          // Restructure as needed.
          $output['site'][substr($key, $site_px_len)] = $value;
        } else {
          // Unchanged.
          $output[$key] = $value;
        }
      }
    }
    return $output;
  }

  public function conventionallyTransformIndex($input)
  {
    return array_map(function ($item) {
      return array(
        'id' => $item['id'],
        'title' => $item['title'],
        'year' => $item['year'],
        'section_name' => $item['section'],
        'section' => array(
          'id' => $item['secid'],
          'name' => $item['sec_desc'],
          'folder_name' => $item['section'],
          'should_display_name' => $item['sec_disp'],
        ),
      );
    }, $input);
  }

}
