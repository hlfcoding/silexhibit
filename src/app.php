<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['env'] = getenv('APP_ENV') ?: 'dev';
$app['root'] = __DIR__.'/../';
$app['config'] = require $app['root'].'config/'.$app['env'].'.php';
$app['debug'] = $app['env'] === 'dev';

$app->register(new Silexhibit\DataBaseServiceProvider());

$app->register(new Silexhibit\DataAdapterServiceProvider());

$app->register(new Silexhibit\ThemeServiceProvider());

$app->mount('/', new Silexhibit\SiteControllerProvider());

return $app;
