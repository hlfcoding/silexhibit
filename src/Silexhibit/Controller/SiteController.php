<?php

namespace Silexhibit\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Igorw\Silex\ConfigServiceProvider;

use Assetic\Asset\FileAsset,
    Assetic\Asset\GlobAsset;

use Silexhibit\Controller,
    Silexhibit\Model\ExhibitModel,
    Silexhibit\ModelServiceProvider,
    Silexhibit\View\ExhibitView,
    Silexhibit\ViewServiceProvider;

class SiteController extends Controller
{
  public $app_name = 'site';

  protected $exhibit_model;
  protected $exhibit_view;

  public function __construct(Application $app)
  {
    $this->templater = true;
    $this->component_names = array('exhibit', 'plugin');

    parent::__construct($app);

    $this->exhibit_model = new ExhibitModel($this);
    $app->register($this->exhibit_model);

    $this->templater = $this->registerTemplater(
      $app, 'templater', array(
        'template_path' => "{$this->paths['app_web']}/mustache",
        'partial_path' => "{$this->paths['theme_web']}/mustache",
      ));
    $this->registerComponentTemplaters($app);

    $this->exhibit_view = $this->registerView(new ExhibitView($this), $app);

    $this->component_factories = array();
    foreach ($this->component_names as $component) {
      $this->component_factories[$component] = isset($app["$component.factory"]) ? $app["$component.factory"] : null;
    }

  }

  protected function registerPaths(Application $app)
  {
    parent::registerPaths($app);
    $this->paths['exhibit_web'] = "{$this->paths['app_web']}/exhibit";
    $this->paths['plugin_web'] = "{$this->paths['app_web']}/plugin";
  }

  protected function registerConfig(Application $app)
  {
    parent::registerConfig($app);
    foreach ($this->getComponentPaths() as $component_paths) {
      foreach ($component_paths as $path) {
        $app->register(new ConfigServiceProvider("$path/main.yml"));
      }
    }
  }

  protected function getComponentPaths()
  {
    $paths = array();
    foreach ($this->component_names as $component) {
      $component_root = $this->paths["${component}_web"];
      $files = scandir($component_root);
      $paths[$component] = array();
      foreach ($files as $file) {
        $path = "$component_root/$file";
        if (is_dir($path) && strpos($file, '.') !== 0) {
          $paths[$component][] = $path;
        }
      }
    }
    return $paths;
  }

  public function modelForView(ViewServiceProvider $view)
  {
    if ($view instanceof ExhibitView) {
      return $this->exhibit_model;
    }
    return null;
  }
  public function viewForModel(ModelServiceProvider $model)
  {
    if ($view instanceof ExhibitModel) {
      return $this->exhibit_view;
    }
    return null;
  }

  public function provideData($key, array $params=array())
  {
    switch ($key) {
      case 'exhibit_index':
        return $this->exhibit_model->fetchIndexArray($params['type']);
      case 'exhibit_status_names':
        return ExhibitModel::$status_names[$params['status']];
      default: return null;
    }
  }

  protected function willRegisterCSSAssets($assets)
  {
    $modifier = '';
    $structure = array(&$assets, &$modifier);
    if ($this->is_prod) {
      return $structure;
    }
    $modifier = $this->registerComponentAssets('scss', $assets);
    return $structure;
  }

  protected function willRegisterJSAssets($assets)
  {
    $modifier = '';
    $structure = array(&$assets, array('main' => &$modifier));
    if ($this->is_prod) {
      return $structure;
    }
    $modifier = $this->registerComponentAssets('coffee', $assets['main']);
    return $structure;
  }

  protected function registerComponentAssets($type, &$assets)
  {
    $fm = $this->asset_factory->getFilterManager();
    $pattern = ($type === 'scss') ? 'main' : '*';
    $modifier = '';
    foreach ($this->component_names as $component) {
      if (!isset($this->component_factories[$component])) {
        continue;
      }
      $factory = $this->component_factories[$component];
      $names = $factory->getNames();
      if (empty($names)) {
        continue;
      }
      sort($names);
      $path = $this->paths["${component}_web"];
      $this->registerExtraLibsForComponent($type, $assets, $factory);
      if ($type === 'coffee') {
        $this->registerExtraLibsForComponent($type, $assets, $factory, true);
      }
      foreach ($names as $name) {
        $assets[] = new GlobAsset("$path/$name/$pattern.$type", array($fm->get($type)));
        $includes_path = "$path/$name/$type";
        if (is_dir($includes_path)) {
          $assets[] = new GlobAsset("$includes_path/*", array($fm->get($type)));
        }
      }
      $names = array_map(function ($name) {
        return substr($name, 0, 1).(strlen($name) - 2).substr($name, -1);
      }, $names);
      $modifier .= '-'.implode('-', $names);
    }
    return !empty($modifier) ? ".inc$modifier" : $modifier;
  }
  protected function registerExtraLibsForComponent($type, &$assets, $factory, $keep_type=false)
  {
    if (!isset($factory)) {
      return;
    }
    $names = $factory->getNames();
    if (empty($names)) {
      return;
    }
    sort($names);
    $lib_type = $type;
    if (!$keep_type) {
      $lib_type = ($type === 'scss') ? 'css' : 'js';
    }
    $libs_path = "{$this->paths['lib_web']}/$lib_type/optional";
    if (!is_dir($libs_path)) {
      return;
    }
    $extra_libs = array();
    $am = $this->asset_factory->getFilterManager();
    $filters = ($am->has($type) && $keep_type) ? array($am->get($type)) : array();
    foreach ($names as $name) {
      $instance = $factory->getInstanceById($name);
      if (is_array($instance)) {
        $config = $instance['config'];
      } else {
        $config = $instance->config;
      }
      if (!isset($config['view'])) {
        continue;
      }
      if (isset($config['view'][$lib_type])) {
        $extra_libs = array_merge($extra_libs, $config['view'][$lib_type]);
      }
    }
    $extra_libs = array_unique($extra_libs);
    foreach ($extra_libs as $relative_path) {
      $full_path = "$libs_path/$relative_path.$lib_type";
      $assets[] = new FileAsset($full_path, $filters);
    }
  }

  // Actions
  // -------

  public function indexAction()
  {
    $data = $this->exhibit_model->fetchAssoc('/');
    if (!$data) {
      // TODO: Handle home-less error.
    }
    return $this->exhibit_view->render($data);
  }

  public function projectAction($section, $project)
  {
    $data = $this->exhibit_model->fetchAssoc($project, $section);
    if (!$data) {
      return new RedirectResponse($this->url->generate('homepage'), 302);
    }
    return $this->exhibit_view->render($data);
  }

  public function customPageAction($page)
  {
    $data = $this->exhibit_model->fetchAssoc($page);
    if (!$data) {
      return new RedirectResponse($this->url->generate('homepage'), 302);
    }
    return $this->exhibit_view->render($data);
  }

  public function pluginEndpointAction($plugin, $endpoint)
  {
    $name = $plugin;
    $plugin = $this->component_factories['plugin']->getPlugin($name);
    if (!isset($plugin)) {
      $plugin = $this->component_factories['plugin']->registerPlugin($name, $this);
      // TODO: Invalid.
    }
    return $plugin->run(array(
      'plugin_endpoint' => $endpoint
    ));
  }

}
