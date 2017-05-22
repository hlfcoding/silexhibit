<?php

namespace Silexhibit;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataBaseServiceProvider implements ServiceProviderInterface {

  protected $dbal;
  protected $tbl;

  public function register(Container $app) {
    $this->tbl = $app['config']['db']['table_prefix'];
    $this->dbal = $app['db'];
    $app['database'] = $this;
  }

  public function selectExhibit($column, $value, $public = false) {
    $record = $this->selectPost($column, $value, $public);
    if (!empty($record)) {
      $record['exhibit'] = $this->selectExhibitMedia($record['id']);
    }
    return $record;
  }

  public function selectExhibitMedia($id) {
    $query = "SELECT *
      FROM {$this->tbl}media AS m
      WHERE m.media_ref_id = ?
        AND m.media_obj_type = 'exhibit'
      ORDER BY m.media_order ASC,
               m.media_id ASC";
    return $this->dbal->fetchAll($query, array($id));
  }

  public function selectPost($column, $value, $public = false) {
    $query = "SELECT *
      FROM {$this->tbl}objects AS o,
           {$this->tbl}objects_prefs AS op
      WHERE o.$column = ?
      AND o.object = op.obj_ref_type";
    if ($public) {
      $query .= " AND o.status = '1'";
    }
    return $this->dbal->fetchAssoc($query, array($value));
  }

}
