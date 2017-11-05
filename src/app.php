<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['env'] = getenv('APP_ENV') ?: 'dev';
$app['root'] = __DIR__.'/../';
$app['config'] = require $app['root'].'config/'.$app['env'].'.php';
$app['debug'] = $app['env'] === 'dev';

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
  'db.options' => array(
    'charset' => 'utf8',
    'dbname' => $app['config']['db']['name'],
    'driver' => 'pdo_mysql',
    'host' => $app['config']['db']['host'],
    'password' => $app['config']['db']['password'],
    'user' => $app['config']['db']['user'],
  )
)); // 'db'

$app->register(new Silexhibit\DataBaseServiceProvider());

$app->register(new Silexhibit\DataAdapterServiceProvider());

$app->mount('/', new Silexhibit\SiteControllerProvider());

return $app;
