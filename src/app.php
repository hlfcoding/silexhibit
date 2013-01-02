<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app['templater'] = $app->share(function () {
  return new Mustache_Engine;
});

$app->mount('/', include 'controllers/site.php');
$app->mount('/ndxz_studio', include 'controllers/cms.php');

return $app;
