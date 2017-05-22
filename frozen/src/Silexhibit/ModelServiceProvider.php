<?php

// Silexhibit Model
// ================
// This is the base model class. It can be used as a Silex service provider. Its
// main responsibilities include transporting data to and from the database via
// the Doctrine DBAL service provider, and validating that data as needed. It
// acts as a repository for only one type of object, although it may deal with
// more than one table. Certain data may also come from other sources, like
// configuration option groups or static and constant properties.

namespace Silexhibit;

use Silex\Application;
use Silex\ServiceProviderInterface;

// Required Silex application globals, helpers, and services:
// `db`, `logger`.

abstract class ModelServiceProvider implements ServiceProviderInterface
{
  const DATE_FORMAT = 'Y-m-d';
  const DATETIME_FORMAT = 'Y-m-d H:i:s';

  // Core Configuration
  // ------------------

  // For the model to be able to register as a Silex service provider,
  // `service_name` must be set before calling the constructor. Conventionally,
  // it can be set by redeclaring the property. Conventionally, dot notation
  // should be used.
  protected $service_name;
  // `tbl` is used internally to abstract able the database table name prefix.
  protected $tbl;

  // Bundled Services
  // ----------------
  
  // `db` is a reference to the main database service provider. While the base
  // model is database-agnostic and any data layer can be used per the subclass'
  // implementation, currently, the Doctrine DBAL is conventional.
  protected $db;
  // `logger` is useful for checking the results of database operations.
  protected $logger;
  // `validator` is a reference to the main validator service provider. Model
  // subclasses need to provide a properly configured validation constraint via
  // `getValidationConstraint`. The validator is optional and not providing it
  // means model validation is disabled.
  protected $validator;
  // Conventionally, the model should know about its `controller`. This tight
  // coupling means the model should only have one controller, which acts as its
  // delegate.
  protected $controller;

  // Other
  // -----

  // A list object of constraint violations for the last validation. These will
  // be reset on each call to `validateAssoc`.
  protected $validation_errors;

  // Constructor
  // -----------
  // Conventionally, `controller` is required for the model to fully function.
  public function __construct($controller=null)
  {
    // - Store reference to given `controller`.
    if ($controller) {
      $this->controller = $controller;
    }
  }

  // Silex Integration
  // -----------------

  // Conventionally, `register` should contain logic for setup and aliasing
  // related the dependencies from `app`.
  public function register(Application $app)
  {
    // - Alias services and globals.
    $this->tbl = $app['db.info']['table_prefix'];
    $this->db = $app['db'];
    $this->logger = $app['logger'];
    if (isset($app['validator'])) {
      $this->validator = $app['validator'];
    }
    // - Publish self as service.
    $app[$this->service_name] = $this;
  }
  public function boot(Application $app)
  {
  }

  // Data Transport API
  // ------------------
  // For sample methods, refer to `ExhibitModel`. In general, data should be
  // conventionally returned in the array format.

  // Validation API
  // --------------

  // `getValidationConstraint` should return the configured instance of
  // `Symfony\Component\Validator\Constraint`, and conventionally should be a
  // `Collection` that can group together other constraints.
  abstract protected function getValidationConstraint();

  // `validateAssoc` simply validates with `validator` the given `data` and
  // updates `validation_errors`. Setting the `partial` flag means to validation
  // constraint will temporarily `allowMissingFields` until it's true.
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

  // `getValidationErrorJSON` transforms the error objects `validation_errors`
  // to a key-value format, where the key is the property name and the value is
  // the message.
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
