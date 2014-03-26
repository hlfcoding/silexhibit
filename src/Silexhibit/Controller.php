<?php

namespace Silexhibit;

use Silex\Application;
use Symfony\HttpFoundation\Request;

use Igorw\Silex\ConfigServiceProvider;

use Mustache_Engine,
    Mustache_Loader_FilesystemLoader;

use Assetic\Asset\AssetCache,
    Assetic\Asset\AssetCollection,
    Assetic\Asset\FileAsset,
    Assetic\Asset\GlobAsset,
    Assetic\AssetManager,
    Assetic\AssetWriter,
    Assetic\Cache\FilesystemCache,
    Assetic\Factory\AssetFactory,
    Assetic\Factory\Worker\CacheBustingWorker,
    Assetic\Filter\CoffeeScriptFilter,
    Assetic\Filter\GoogleClosure\CompilerApiFilter as JsCompressorFilter,
    Assetic\Filter\Sass\ScssFilter,
    Assetic\Filter\Yui\CssCompressorFilter,
    Assetic\FilterManager;

use Silexhibit\ModelServiceProvider,
    Silexhibit\ViewServiceProvider;

abstract class Controller
{
  protected $debug;
  protected $is_prod;

  protected $options;
  protected $is_themable;
  protected $theme_options;
  protected $url;
  protected $logger;

  public $templater;
  public $string_templater;
  protected $component_templaters;
  protected $component_factories;
  protected $component_names;

  protected $paths;

  protected $asset_config;
  protected $asset_manager;
  protected $asset_factory;
  protected $asset_writer;
  protected $asset_cache;
  public $asset_urls;

  public function __construct(Application $app)
  {
    $this->debug = $app['debug'];
    $this->is_prod = $app['env'] === 'prod';

    $app_name = $this->app_name;
    if (!isset($this->component_names)) {
      $this->component_names = array();
    }

    $this->registerPaths($app);
    $this->registerConfig($app);
    $this->paths['theme_web'] = rtrim($app['path.theme_web'](), '/');

    $this->url = $app['url_generator'];
    $this->logger = $app['logger'];
    $this->string_templater = $app['mustache'];

    if (!isset($this->templater)) {
      $this->templater = $this->registerTemplater(
        $app, 'templater', array(
          'template_path' => "{$this->paths['app_web']}/mustache",
          'partial_path' => "{$this->paths['app_web']}/mustache/partial",
        ));
    }

    $this->asset_config = $app['merged']('assetic');
    $this->setupAssets();
  }

  abstract public function modelForView(ViewServiceProvider $view);
  abstract public function viewForModel(ModelServiceProvider $model);

  public function viewWillRender(ViewServiceProvider $view)
  {
    $this->processAssets();
  }

  abstract public function provideData($key, array $params=array());

  protected function registerPaths(Application $app)
  {
    $app_name = $this->app_name;
    $app_controller = $this;
    $app['path.app_web'] = $app->protect(function ($sub_path='') use ($app, $app_name) {
      return $app['path']("web/{$app_name}/$sub_path");
    });
    $app['path.theme_web'] = $app->protect(function ($sub_path='') use ($app, $app_name, $app_controller) {
      return $app_controller->is_themable
        ? $app['path']("web/{$app_name}/{$app['app.opts']['theme']}/$sub_path")
        : $app['path.app_web']();
    });
    $this->paths = array(
      'app_web' => rtrim($app['path.app_web'](), '/'),
      'cache' => $app['path.cache']('assetic'),
      'dist' => rtrim($app['path.asset_dist'](), '/'),
      'lib_web' => rtrim($app['path.asset_lib'](), '/'),
    );
  }

  protected function registerView(ViewServiceProvider $view, Application $app)
  {
    $app->register($view);
    $view->templater = $this->templater;
    $view->string_templater = $this->string_templater;
    $view->component_templaters = $this->component_templaters;
    return $view;
  }

  protected function registerComponentTemplaters(Application $app)
  {
    foreach ($this->component_names as $component) {
      if (!isset($this->paths["${component}_web"])) {
        continue;
      }
      $this->component_templaters[$component] = $this->registerTemplater(
        $app, "$component.templater", array(
          'template_path' => $this->paths["${component}_web"],
        ));
    }
  }

  protected function registerConfig(Application $app)
  {
    $config_vars = $app['config.vars'];

    $app->register(new ConfigServiceProvider($app['path.app_web']('config/common.yml'), $config_vars));
    $app->register(new ConfigServiceProvider($app['path.app_web']("config/{$app['env']}.yml"), $config_vars));
    $this->options = $app['app.opts'] = array_merge_recursive(
      $app['merged']('app'),
      $app['merged']($this->app_name)
    );

    $this->is_themable = isset($app['app.opts']['theme']);

    if ($this->is_themable) {
      $app->register(new ConfigServiceProvider($app['path.theme_web']('config/common.yml'), $config_vars));
      $app->register(new ConfigServiceProvider($app['path.theme_web']("config/{$app['env']}.yml"), $config_vars));
      $this->theme_options = $app['theme.opts'] = $app['merged']("theme.{$app['app.opts']['theme']}");
    }
  }

  protected function registerTemplater(Application $app, $service_name, $opts)
  {
    $app[$service_name] = $app->share(function () use ($app, $opts) {
      $tpl_opts = array(
        'loader' => new Mustache_Loader_FilesystemLoader($opts['template_path']),
      );
      if (isset($opts['partial_path'])) {
        $tpl_opts['partials_loader'] = new Mustache_Loader_FilesystemLoader($opts['partial_path']);
      }
      if ($app['env'] === 'prod') {
        $tpl_opts['cache'] = $app['path.cache']('mustache');
      }
      return new Mustache_Engine($tpl_opts); // Uses custom auto-loader.
    });
    return $app[$service_name];
  }

  protected function setupAssets()
  {
    $this->asset_urls = array(
      'css' => array(),
      'head_js' => array(),
      'js' => array(),
    );

    $conf = $this->asset_config;

    $am = $this->asset_manager = new AssetManager();

    $fm = new FilterManager();
    $fm->set('jsmin', new JsCompressorFilter());
    $fm->set('cssmin', new CssCompressorFilter($conf['css_compressor']));
    $fm->set('coffee', new CoffeeScriptFilter($conf['coffee']));
    $fm->set('scss', new ScssFilter());

    $aw = $this->asset_writer = new AssetWriter($this->paths['dist']);

    $af = $this->asset_factory = new AssetFactory('', $this->debug);
    $af->setFilterManager($fm);
    $af->setAssetManager($am);
    if ($conf['cache_bust']) {
      $af->addWorker(new CacheBustingWorker());
    }

    $ac = $this->asset_cache = new FilesystemCache($this->paths['cache']);
  }

  abstract protected function willRegisterCSSAssets($assets);
  abstract protected function willRegisterJSAssets($assets);

  protected function processAssets()
  {
    $p = $this->paths;
    $app_name = $this->app_name;

    $af = $this->asset_factory;
    $fm = $this->asset_factory->getFilterManager();
    $am = $this->asset_manager;
    $ac = $this->asset_cache;
    $min = ($this->debug ? '' : '.min');

    $app_assets = $theme_assets = array(
      'coffee' => array(),
      'css' => array(),
      'js' => array(),
    );
    if (isset($this->options['view'])) {
      $app_assets = $this->addOptionalAssets($app_assets, $this->options['view']);
    }
    if (isset($this->theme_options) && isset($this->theme_options['view'])) {
      $theme_assets = $this->addOptionalAssets($theme_assets, $this->theme_options['view']);
    }

    // Should be via S3 on prod.
    $css_assets = $this->is_prod ? array() : array_merge(
      array(
        new GlobAsset(array(
          "{$p['lib_web']}/css/*.css",
          "{$p['lib_web']}/css/plugins/*.css",
        )),
        new FileAsset("{$p['app_web']}/scss/style.scss", array($fm->get('scss')))
      ),
      $app_assets['css'],
      $theme_assets['css']
    );
    list($css_assets, $css_modifier) = $this->willRegisterCSSAssets($css_assets);
    if ($this->is_themable) {
      $css_assets[] = new FileAsset("{$p['theme_web']}/scss/style.scss", array($fm->get('scss')));
    }
    $css_filters = $this->debug ? array() : array($fm->get('cssmin'));
    $css_cache = new AssetCache(new AssetCollection($css_assets, $css_filters), $ac);
    $css_cache->setTargetPath("${app_name}${css_modifier}.compiled$min.css");
    $am->set('all_css', $css_cache);
    $this->asset_urls['css'][] = "/dist/{$css_cache->getTargetPath()}";

    $js_assets = array();
    $js_assets['head'] = $this->is_prod ? array() : array( // Should be via S3 on prod.
      new GlobAsset(array(
        "{$p['lib_web']}/js/head/*.js",
      )),
    );
    $js_assets['lib'] = $this->is_prod ? array() : array( // Should be via S3 on prod.
      new GlobAsset(array(
        "{$p['lib_web']}/js/*.js",
        "{$p['lib_web']}/js/development/*.js",
      )),
      new GlobAsset(array(
        "{$p['lib_web']}/coffee/*.coffee",
      ), array($fm->get('coffee'))),
    );
    // Should be via S3 on prod.
    $js_assets['main'] = $this->is_prod ? array() : array_merge(
      $app_assets['coffee'],
      $app_assets['js'],
      array(
        new GlobAsset(array(
          "{$p['app_web']}/coffee/*.coffee",
          "{$p['app_web']}/coffee/main.coffee",
          "{$p['app_web']}/coffee/**/*.coffee",
        ), array($fm->get('coffee'))),
      ),
      $theme_assets['coffee'],
      $theme_assets['js']
    );
    list($js_assets, $js_modifiers) = $this->willRegisterJSAssets($js_assets);
    if ($this->is_themable) {
      $js_assets['main'][] = new GlobAsset("{$p['theme_web']}/coffee/*.coffee", array($fm->get('coffee')));
    }
    $js_filters = $this->debug ? array() : array($fm->get('jsmin'));
    $js_map = array('head' => 'head', 'lib' => 'lib', 'main' => '');
    $js_path = null;
    foreach ($js_map as $name => $file_modifier) {
      $js_cache = new AssetCache(new AssetCollection($js_assets[$name], $js_filters), $ac);
      $file_modifier = !empty($file_modifier) ? "-$file_modifier" : '';
      $file_modifier .= isset($js_modifiers[$name]) ? $js_modifiers[$name] : '';
      $js_cache->setTargetPath("${app_name}${file_modifier}.compiled$min.js");
      $am->set("{$name}_js", $js_cache);
      if ($name === 'head') {
        $this->asset_urls['head_js'][] = "/dist/{$js_cache->getTargetPath()}";
      } else {
        $this->asset_urls['js'][] = "/dist/{$js_cache->getTargetPath()}";
      }
    }

    if (!$this->is_prod) {
      $this->asset_writer->writeManagerAssets($am);
    }

  }

  protected function addOptionalAssets(array $assets, array $view_options)
  {
    $p = $this->paths;
    $fm = $this->asset_factory->getFilterManager();

    foreach ($view_options as $type => $requests) {
      if (!isset($assets[$type])) {
        continue;
      }
      $filters = $fm->has($type) ? array($fm->get($type)) : array();
      $assets[$type] = array_map(function ($relative_path) use ($p, $type, $filters) {
        $full_path = "{$p['lib_web']}/$type/optional/$relative_path.$type";
        return (strpos($relative_path, '*') !== false)
          ? new GlobAsset($full_path, $filters) : new FileAsset($full_path, $filters);
      }, $requests);
    }
    return $assets;
  }
}
