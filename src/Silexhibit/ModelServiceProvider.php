<?php

namespace Silexhibit;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ModelServiceProvider implements ServiceProviderInterface
{
  const DATE_FORMAT = 'Y-m-d';
  const DATETIME_FORMAT = 'Y-m-d H:i:s';

  protected $tbl;
  protected $db;
  protected $logger;

  protected $controller;

  protected $validator;
  protected $validation_constraint;
  protected $validation_errors;

  protected $service_name;

  public function __construct($controller=null, $validator=null)
  {
    if ($controller) {
      $this->controller = $controller;
    }
    if ($validator) {
      $this->validator = $validator;
    }
  }

  public function register(Application $app)
  {
    $this->tbl = $app['db.info']['table_prefix'];
    $this->db = $app['db'];
    $this->logger = $app['logger'];
    $app[$this->service_name] = $this;
  }
  public function boot(Application $app)
  {
  }

  public function validateAssoc($data, $partial=false)
  {
    if (!isset($this->validator)) {
      throw new Exception('Validator required.');
    }
    $constraint = $this->getValidationConstraint();
    $shouldToggleMissing = $partial && !$constraint->allowMissingFields;
    if ($shouldToggleMissing) {
      $constraint->allowMissingFields = true;
    }
    $this->validation_errors = $this->validator->validateValue($data, $constraint);
    if ($shouldToggleMissing) {
      $constraint->allowMissingFields = false;
    }
    return !count($this->validation_errors);
  }

  public function getValidationErrorJSON()
  {
    if (!isset($this->validator)) {
      throw new Exception('Validator required.');
    }
    $errors = array();
    foreach ($this->validation_errors as $error) {
      $errors[$error->getPropertyPath()] = $error->getMessage();
    }
    return $errors;
  }
}
