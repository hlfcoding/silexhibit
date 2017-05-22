<?php

// Silexhibit Application
// ======================
// This script builds off of the original Silex example application. It sets up
// the app dependency container with globals, helpers, configuration, service
// providers (both vendor and framework), and endpoints for all applications.

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

// Foundation
// ----------

// `env` is a global set in the htaccess file. Setting to `dev` triggers
// additional logging and debugging features. _Switch to `prod` in the htaccess
// when deploying._
$app['env'] = getenv('APP_ENV') ?: 'dev';
// `path` is a helper for resolving a relative `sub_path` from the install root
// to be absolute.
$app['path'] = $app->protect(function ($sub_path) use ($app) {
  return __DIR__."/../$sub_path";
});
// `path.asset_dist` is a helper wrapping around `path` where `sub_path` is
// expected to be relative to `<root>/web/dist`. It acts as the sole source of
// truth for the dist path.
$app['path.asset_dist'] = $app->protect(function ($sub_path='') use ($app) {
  return $app['path']("web/dist/$sub_path");
});
// `path.asset_lib` is a helper wrapping around `path` where `sub_path` is
// expected to be relative to `<root>/web/lib`. It acts as the sole source of
// truth for the lib path.
$app['path.asset_lib'] = $app->protect(function ($sub_path='') use ($app) {
  return $app['path']("web/lib/$sub_path");
});
// `path.cache` is a helper wrapping around `path` where `sub_path` is
// expected to be relative to `<root>/tmp/cache`. It acts as the sole source of
// truth for the cache path.
$app['path.cache'] = $app->protect(function ($sub_path='') use ($app) {
  return $app['path']("tmp/cache/$sub_path");
});
// `merged` is a core helper for providing final, merged configuration option
// group. Given an `opt_key`, return a merged copy of the option group with the
// common option group as base and other option groups overriding it as needed.
// Option groups are added as a globals via ConfigServiceProvider. Currently,
// other options are only environment-specific options. Keys for any common
// option groups that can be merged should be suffixed with `.common`. Keys for
// environment-specific option groups should be suffixed with `.dev` and
// `.prod`. Conventionally, this is used for getting configuration for distinct
// feature-sets.
$app['merged'] = $app->protect(function ($opt_key) use ($app) {
  $env_opts = isset($app["$opt_key.{$app['env']}"]) ? $app["$opt_key.{$app['env']}"] : array();
  $env_opts = is_array($env_opts) ? $env_opts : array();
  $opts = array_merge_recursive($app["$opt_key.common"], $env_opts);
  if ($app['debug'] && $app['logger']) {
    // Turn off debug logging for now.
    # $app['logger']->info($opt_key, $opts);
  }
  return $opts;
});
// Create local aliases for helpers.
$path = $app['path'];
$merged = $app['merged'];
// `config.vars` is a global containing dynamic replacements for static
// configuration loaded from the files via `ConfigServiceProvider`.
$app['config.vars'] = array(
  'logs_path' => $path('logs'),
  'web_baseurl' => '',
);

// Shared Vendor Services
// ----------------------

// First register root-level common configuration option groups.
$app->register(new ConfigServiceProvider($path('config/common.yml'), $app['config.vars']));
// Then register root-level environment configuration option groups. May override any non-
// environment option groups as needed.
$app->register(new ConfigServiceProvider($path("config/{$app['env']}.yml"), $app['config.vars']));
// Use MustachePHP for templating.
$app->register(new MustacheServiceProvider());

// Shared Silex Services
// ---------------------

// For all Silexhibit applications, include url-generation, http caching,
// logging, Doctrine DBAL, and controller-as-service-provider features. Provide
// merged configuration option groups for service providers as needed.
$app->register(new UrlGeneratorServiceProvider());
$app->register(new HttpCacheServiceProvider());
$app->register(new MonologServiceProvider(), $merged('monolog.options'));
$app->register(new DoctrineServiceProvider(), array('db.options' => $merged('db.options')));
$app->register(new ServiceControllerServiceProvider());

// Shared Custom Services
// ----------------------

// Create a `site.controller` service provider for the Site application.
$app['site.controller'] = $app->share(function () use ($app){
  return new SiteController($app);
});
// Create a `cms.controller` service provider for the CMS application.
$app['cms.controller'] = $app->share(function () use ($app){
  return new CMSController($app);
});

// CMS Endpoints
// -------------
// All API endpoints return JSON. These endpoints are first because they have
// static initial URL fragments.

// `/st-studio/{*}` - Compound endpoint where all routes point to the client-
// side CMS web application. Since route patterns don't support globbing or
// regex patterns, wrap endpoint declaration with a loop and use the index to
// make route names unique.
foreach (array(
  '/st-studio/',
  '/st-studio/exhibits',
  '/st-studio/exhibit/{id}',
) as $i => $route) {
  $app->get($route, 'cms.controller:indexAction')
      ->bind("dashboard_$i");
}
// `/st-exhibit/`: API endpoint for listing all exhibits.
$app->get('/st-exhibit/', 'cms.controller:exhibitListAction')
    ->bind('exhibits');
// `/st-exhibit/{id}`: API endpoint for exhibit CRUD.
$app->match('/st-exhibit/{id}', 'cms.controller:exhibitAction')
    ->method('GET|POST|PUT|PATCH|DELETE')
    ->convert('id', function ($id) { return (int)$id; })
    ->bind('exhibit');
// `/st-exhibit/{id}/background-image`: API endpoint for exhibit background
// image CRUD.
$app->match('/st-exhibit/{id}/background-image', 'cms.controller:exhibitBackgroundImageAction')
    ->method('POST|PUT|DELETE')
    ->convert('id', function ($id) { return (int)$id; })
    ->bind('exhibit_background_image');
// `/st-setting/{type}`: API endpoint for site settings.
$app->match('/st-setting/{type}', 'cms.controller:settingAction')
    ->method('GET|POST|PUT|DELETE')
    ->bind('setting');

// Site Endpoints
// --------------
// All API endpoints return JSON. These endpoints are last because they (at
// least and except the homepage) have dynamic initial URL fragments.

// `/` - Site homepage endpoint.
$app->get('/', 'site.controller:indexAction')
    ->bind('homepage');
// `/st-{plugin}/{endpoint}/` - API endpoint shared between Site plugins. The
// first fragment is dynamic based on the plugin name. The second fragment is a
// virtual 'endpoint' for the plugin to use as fit.
$app->get('/st-{plugin}/{endpoint}/', 'site.controller:pluginEndpointAction')
    ->bind('plugin_endpoint');
// `/{section}/{project}/` - Site project exhibit endpoint.
$app->get('/{section}/{project}/', 'site.controller:projectAction')
    ->bind('project_page');
// `/{page}/` - Site page exhibit endpoint.
$app->get('/{page}/', 'site.controller:customPageAction')
    ->bind('custom_page');

// Return our local reference `app`, so it can be run.
return $app;
