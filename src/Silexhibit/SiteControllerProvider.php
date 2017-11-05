<?php

namespace Silexhibit;

use Mustache\Silex\Provider\MustacheServiceProvider;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SiteControllerProvider implements ControllerProviderInterface {

  protected $config;

  public function connect(Application $app) {
    $controllers = $app['controllers_factory'];
    $controllers->before(function($request, $app) {
      $this->registerServiceProviders($app);
    });
    $controllers->get('/', function(Application $app) {
      $exhibit = $app['database']->selectExhibit('url', '/');
      return $this->renderExhibit($exhibit, $app);
    });
    $controllers->get('/{section}/{project}/', function(Application $app, Request $request, $section, $project) {
      $exhibit = $app['database']->selectExhibit('url', $request->getPathInfo());
      if (empty($exhibit)) { return $app->redirect('/'); }
      return $this->renderExhibit($exhibit, $app);
    });
    $controllers->get('/{page}/', function(Application $app, Request $request, $page) {
      $exhibit = $app['database']->selectExhibit('url', $request->getPathInfo());
      if (empty($exhibit)) { return $app->redirect('/'); }
      return $this->renderExhibit($exhibit, $app);
    });
    $controllers->after(function($request, $response, $app) {
      $this->wrapContent($response, $app);
    });
    return $controllers;
  }

  protected function registerServiceProviders($app) {
    $app->register(new MustacheServiceProvider(), array(
      'mustache.path' => $app['root'].'src/mustache',
      'mustache.options' => array(
        'cache' => $app['root'].'tmp/cache/mustache',
      ),
    )); // 'mustache'
  }

  protected function renderExhibit($exhibit, $app) {
    $exhibit = $app['adapter']->conventionalExhibit($exhibit);
    $this->config = $exhibit['site'];
    return $app['theme']->renderExhibit($exhibit, $app);
  }

  protected function renderIndex($index, $type, $app) {
    $index = $app['adapter']->conventionalExhibitIndex($index, array('type' => $type));
    return $app['theme']->renderIndex($index, $type, $app);
  }

  protected function wrapContent($response, $app) {
    $index = $app['database']->selectIndex($this->config['index_type'], true);
    $content = $app['theme']->wrapContent(array(
      'body' => $response->getContent(),
      'index' => $this->renderIndex($index, $this->config['index_type'], $app),
    ), $app);
    return $response->setContent(
      $app['mustache']->render('theme/layout', $content)
    );
  }

}
