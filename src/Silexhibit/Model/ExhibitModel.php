<?php

namespace Silexhibit\Model;

use Silex\Application;

use Symfony\Component\Validator\Constraints as Constraint,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Finder\SplFileInfo as File;

use Silexhibit\ModelServiceProvider as Model;
use Silexhibit\Traits\HTMLProcessingTrait;

class ExhibitModel extends Model
{
  use HTMLProcessingTrait;

  const INDEX_CHRONOLOGICAL = 1;
  const INDEX_SECTIONAL = 2;

  const STATUS_DRAFT = 0;
  const STATUS_PUBLISHED = 1;

  static $type_names = array(
    self::INDEX_CHRONOLOGICAL => 'chronological',
    self::INDEX_SECTIONAL => 'sectional',
  );

  static $status_names = array(
    self::STATUS_DRAFT => 'draft',
    self::STATUS_PUBLISHED => 'published',
  );

  public $is_public = true;

  protected $service_name = 'exhibit.model';
  protected $config;

  public function register(Application $app)
  {
    parent::register($app);
    $this->exhibits_path = $app['path']('web/site/exhibit');
    $this->config = $app['app.opts']['exhibit'];
  }

  # Fetch Exhibit
  # -------------

  public function fetchAssoc($project=null, $section='')
  {
    if (!$project || empty($project)) {
      throw new Exception('Project slug required.');
    }
    // TODO: Tie this into routing layer.
    $url = (empty($section))
      ? (($project === '/') ? $project : "/$project/")
      : "/$section/$project/";
    $data = $this->fetchPostAssoc($url);
    $data['exhibit'] = $this->db->fetchAll($this->exhibitMediaQuery(), array($data['id']));
    return $data;
  }
  public function fetchPostAssoc($value, $column='url')
  {
    return $this->db->fetchAssoc($this->postQuery($column), array($value));
  }
  protected function postQuery($column='url')
  {
    $query = "SELECT * FROM {$this->tbl}objects AS o, {$this->tbl}objects_prefs AS op
      WHERE o.$column = ?
      AND o.object = op.obj_ref_type";
    if ($this->is_public) {
      $query .= " AND o.status = '1'";
    }
    return $query;
  }
  protected function exhibitMediaQuery()
  {
    return "SELECT * FROM {$this->tbl}media AS m, {$this->tbl}objects_prefs AS op
      WHERE m.media_ref_id = ? AND op.obj_ref_type = 'exhibit' AND op.obj_ref_type = m.media_obj_type
      ORDER BY m.media_order ASC, m.media_id ASC";
  }

  # Fetch Supplemental
  # ------------------

  public function fetchIndexArray($type=self::INDEX_CHRONOLOGICAL)
  {
    $query = "SELECT id, title, content, url,
      section, sec_desc, sec_disp, year, secid
      FROM {$this->tbl}objects as o, {$this->tbl}sections as s
      WHERE o.section_id = s.secid";
    if ($this->is_public) {
      $query .= " AND o.status = '1' AND o.hidden != '1'";
    }
    switch ($type) {
      case self::INDEX_CHRONOLOGICAL:
        $query .= " ORDER BY s.sec_ord ASC, o.year DESC, o.ord ASC";
        break;
      case self::INDEX_SECTIONAL:
        $query .= " ORDER BY s.sec_ord ASC, o.ord ASC";
        break;
      default: break;
    }
    $index = $this->db->fetchAll($query);
    return $index;
  }

  public function fetchAcceptedImageMimes()
  {
    return $this->config['accepted_image_mimes'];
  }

  public function fetchFormats()
  {
    $finder = new Finder();
    $exhibit_dirs = array_values(iterator_to_array(
      $finder->directories()->in($this->exhibits_path)
    ));
    $formats = array_map(function (File $dir) {
      return $dir->getRelativePathname();
    }, $exhibit_dirs);
    return $formats;
  }

  public function fetchImageSizes($type)
  {
    if (!isset($this->config[$type])) {
      throw new \Exception(sprintf(
        "No image sizes of type '$type': %s",
        var_export($this->config, true)
      ));
    }
    $sizes = $this->config[$type];
    return array_map(function ($size) {
      return is_array($size) ? $size : array(
        'name' => $size,
        'value' => $size,
      );
    }, $sizes);
  }

  public function fetchMaxUploadSize()
  {
    $upload_max_filesize = ini_get('upload_max_filesize');
    $upload_max_filesize = preg_replace('/M/', '', $upload_max_filesize);
    $post_max_size = ini_get('post_max_size');
    $post_max_size = preg_replace('/M/', '', $post_max_size);
    return ($post_max_size >= $upload_max_filesize)
        ? "$upload_max_filesize MB"
        : "$post_max_size MB";
  }

  public function updatePostAssoc($data, $id=null)
  {
    $data['udate'] = date(Model::DATETIME_FORMAT, time());
    foreach (array('id', 'status', 'process') as $column) {
      if (!isset($data[$column])) {
        continue;
      }
      $data[$column] = (int)$data[$column];
    }
    if (!$this->validateAssoc($data, true)) {
      return false;
    }
    if (!$id) {
      if (!isset($data['id'])) {
        return false;
      }
      $id = $data['id'];
    }
    if (isset($data['process']) && !!$data['process']) {
      $data['content'] = $this->processHTML($data['content']);
    }
    return $this->db->update(
      "{$this->tbl}objects",
      $data,
      array('id' => $id)
    );
  }

  protected function getValidationConstraint()
  {
    if (!isset($this->validation_constraint)) {
      $this->validation_constraint = new Constraint\Collection(array(
        'id' => new Constraint\Type(array('type' => 'integer')),
        'title' => array(
          new Constraint\Type(array('type' => 'string')),
          new Constraint\Length(array('max' => 144)),
          new Constraint\NotBlank(),
        ),
        'content' => array(
          new Constraint\Type(array('type' => 'string')),
          new Constraint\NotBlank(),
        ),
        'udate' => new Constraint\DateTime(),
        'status' => new Constraint\Type(array('type' => 'integer')),
        'process' => new Constraint\Type(array('type' => 'integer')),
      ));
      $this->validation_constraint->allowExtraFields = true;
    }
    return $this->validation_constraint;
  }

}
