<?php

namespace Silexhibit;

use Mustache\Silex\Provider\MustacheServiceProvider;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteControllerProvider implements ControllerProviderInterface {

  protected $config;
  protected $title;

  public function connect(Application $app) {
    $controllers = $app['controllers_factory'];
    $controllers->before(function(
      Request $request, Application $app
    ) {
      $this->registerServiceProviders($app);
    });
    $controllers->get('/', function(
      Application $app
    ) {
      $exhibit = $app['database']->selectExhibit('url', '/');
      return $this->renderExhibit($exhibit, $app);
    });
    $controllers->get('/{section}/{project}/', function(
      Application $app, Request $request, string $section, string $project
    ) {
      $exhibit = $app['database']->selectExhibit('url', $request->getPathInfo());
      if (empty($exhibit)) { return $app->redirect('/'); }
      return $this->renderExhibit($exhibit, $app);
    });
    $controllers->get('/{page}/', function(
      Application $app, Request $request, string $page
    ) {
      $exhibit = $app['database']->selectExhibit('url', $request->getPathInfo());
      if (empty($exhibit)) { return $app->redirect('/'); }
      return $this->renderExhibit($exhibit, $app);
    });
    $controllers->after(function(
      Request $request, Response $response, Application $app
    ) {
      $this->wrapContent($response, $app);
    });
    return $controllers;
  }

  protected function registerServiceProviders(Application $app) {
    $app->register(new MustacheServiceProvider(), array(
      'mustache.path' => $app['root'].'src/mustache',
      'mustache.options' => array(
        'cache' => $app['root'].'tmp/cache/mustache',
      ),
    )); // 'mustache'
  }

  protected function renderExhibit(array $exhibit, Application $app) {
    $exhibit = $app['adapter']->conventionalExhibit($exhibit);
    $this->config = $exhibit['site'];
    $this->title = $exhibit['title'];
    return $app['theme']->renderExhibit($exhibit, $app);
  }

  protected function renderIndex(array $index, int $type, Application $app) {
    $index = $app['adapter']->conventionalExhibitIndex($index, array('type' => $type));
    return $app['theme']->renderIndex($index, $type, $app);
  }

  protected function wrapContent(Response $response, Application $app) {
    $index = $app['database']->selectIndex($this->config['index_type'], true);
    $content = $app['theme']->wrapContent(array(
      'body' => $response->getContent(),
      'index' => $this->renderIndex($index, $this->config['index_type'], $app),
      'title' => $this->title,
    ), $app);
    return $response->setContent(
      $app['mustache']->render('theme/layout', $content)
    );
  }

}
