<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['env'] = getenv('APP_ENV') ?: 'dev';
$app['config'] = require __DIR__.'/../config/'.$app['env'].'.php';
$app['debug'] = $app['env'] === 'dev';

$app->register(new Mustache\Silex\Provider\MustacheServiceProvider(), array(
  'mustache.path' => __DIR__.'/../web/site/mustache',
  'mustache.options' => array(
      'cache' => __DIR__.'/../tmp/cache/mustache',
  ),
)); // 'mustache'

$app->get('/', function() use($app) {
  return 'Hi';
});

$app->after(function ($request, $response, $app) {
  return $response->setContent(
    $app['mustache']->render('layout', array(
      'body' => $response->getContent(),
      'title' => 'Test',
      'debug_info' => json_encode($app['config']),
    ))
  );
});

return $app;
