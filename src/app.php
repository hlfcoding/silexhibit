<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;

use Igorw\Silex\ConfigServiceProvider;
use Mustache\Silex\Provider\MustacheServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider,
    Silex\Provider\DoctrineServiceProvider,
    Silex\Provider\HttpCacheServiceProvider,
    Silex\Provider\MonologServiceProvider,
    Silex\Provider\UrlGeneratorServiceProvider;

use Silexhibit\Controller\SiteController,
    Silexhibit\Controller\CMSController;

$app = new Application();

// System
// ======

$app['env'] = getenv('APP_ENV') ?: 'dev';
$app['path'] = $app->protect(function ($sub_path) use ($app) {
  return __DIR__."/../$sub_path";
});
$app['path.asset_dist'] = $app->protect(function ($sub_path='') use ($app) {
  return $app['path']("web/dist/$sub_path");
});
$app['path.asset_lib'] = $app->protect(function ($sub_path='') use ($app) {
  return $app['path']("web/lib/$sub_path");
});
$app['path.cache'] = $app->protect(function ($sub_path='') use ($app) {
  return $app['path']("tmp/cache/$sub_path");
});
$app['merged'] = $app->protect(function ($opt_key) use ($app) {
  $env_opts = isset($app["$opt_key.{$app['env']}"]) ? $app["$opt_key.{$app['env']}"] : array();
  $env_opts = is_array($env_opts) ? $env_opts : array();
  $opts = array_merge_recursive($app["$opt_key.common"], $env_opts);
  if ($app['debug'] && $app['logger']) {
    // $app['logger']->info($opt_key, $opts);
  }
  return $opts;
});

$path = $app['path'];
$merged = $app['merged'];

// Shared Services
// ===============

// Vendor
// ------
$app['config.vars'] = array(
  'logs_path' => $path('logs'), // Dynamic config.
  'web_baseurl' => '',
);

$app->register(new ConfigServiceProvider($path('config/common.yml'), $app['config.vars']));
// Overrides any previous configs as needed.
$app->register(new ConfigServiceProvider($path("config/{$app['env']}.yml"), $app['config.vars']));
//
// Built-in
// --------
$app->register(new UrlGeneratorServiceProvider());
$app->register(new HttpCacheServiceProvider());
$app->register(new MonologServiceProvider(), $merged('monolog.options'));
$app->register(new DoctrineServiceProvider(), array('db.options' => $merged('db.options')));
$app->register(new ServiceControllerServiceProvider());
$app->register(new MustacheServiceProvider());
//
// Custom
// ------
$app['site.controller'] = $app->share(function () use ($app){
  return new SiteController($app);
});
$app['cms.controller'] = $app->share(function () use ($app){
  return new CMSController($app);
});

// Endpoints
// =========

// CMS
// ---
foreach (array(
  '/st-studio/',
  '/st-studio/exhibits',
  '/st-studio/exhibit/{id}',
) as $i => $route) {
  $app->get($route, 'cms.controller:indexAction')
      ->bind("dashboard_$i");
}
// API.
$app->get('/st-exhibit/', 'cms.controller:exhibitListAction')
    ->bind('exhibits');
// API.
$app->match('/st-exhibit/{id}', 'cms.controller:exhibitAction')
    ->method('GET|POST|PUT|PATCH|DELETE')
    ->convert('id', function ($id) { return (int)$id; })
    ->bind('exhibit');
// API.
$app->match('/st-exhibit/{id}/background-image', 'cms.controller:exhibitBackgroundImageAction')
    ->method('POST|PUT|DELETE')
    ->convert('id', function ($id) { return (int)$id; })
    ->bind('exhibit_background_image');
// API.
$app->match('/st-setting/{type}', 'cms.controller:settingAction')
    ->method('GET|POST|PUT|DELETE')
    ->bind('setting');
//
// Site
// ----
$app->get('/', 'site.controller:indexAction')
    ->bind('homepage');
// API.
$app->get('/st-{plugin}/{endpoint}/', 'site.controller:pluginEndpointAction')
    ->bind('plugin_endpoint');

$app->get('/{section}/{project}/', 'site.controller:projectAction')
    ->bind('project_page');

$app->get('/{page}/', 'site.controller:customPageAction')
    ->bind('custom_page');


return $app;
