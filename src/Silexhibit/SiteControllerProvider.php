<?php

namespace Silexhibit;

use Mustache\Silex\Provider\MustacheServiceProvider;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SiteControllerProvider implements ControllerProviderInterface {

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
      'mustache.path' => $app['root'].'web/site/mustache',
      'mustache.options' => array(
        'cache' => $app['root'].'tmp/cache/mustache',
      ),
    )); // 'mustache'
  }

  protected function renderExhibit($exhibit, $app) {
    $exhibit = $app['adapter']->conventionalExhibit($exhibit);
    return json_encode($exhibit, JSON_PRETTY_PRINT);
  }

  protected function wrapContent($response, $app) {
    return $response->setContent(
      $app['mustache']->render('layout', array(
        'body' => $response->getContent(),
        'title' => 'Test',
        'debug_info' => json_encode($app['config'], JSON_PRETTY_PRINT),
      ))
    );
  }

}
