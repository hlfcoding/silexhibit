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
    $controllers->before(function(Request $request, Application $app) {
      $this->registerServiceProviders($app);
    });
    $controllers->get('/',
    function(Application $app) {
      $post = $app['database']->selectExhibit('url', '/');
      return $this->renderPost($post, $app);
    });
    $controllers->get('/{section}/{project}/',
    function(Application $app, Request $request, string $section, string $project) {
      $exhibit = $app['database']->selectExhibit('url', $request->getPathInfo());
      if (empty($exhibit)) { return $app->redirect('/'); }
      return $this->renderPost($exhibit, $app);
    });
    $controllers->get('/{page}/',
    function(Application $app, Request $request, string $page) {
      $page = $app['database']->selectPost('url', $request->getPathInfo());
      if (empty($page)) { return $app->redirect('/'); }
      return $this->renderPost($page, $app);
    });
    $controllers->after(function(Request $request, Response $response, Application $app) {
      $this->wrapResponseContent($response, $app);
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

  protected function renderPost(array $post, Application $app) {
    $post = $app['adapter']->conventionalPost($post);
    $this->config = $post['site'];
    $this->title = $post['title'];
    return $app['theme']->renderPost($post, $app);
  }

  protected function renderIndex(array $index, int $type, Application $app) {
    $index = $app['adapter']->conventionalIndex($index, array('type' => $type));
    return $app['theme']->renderIndex($index, $type, $app);
  }

  protected function wrapResponseContent(Response $response, Application $app) {
    $index = $app['database']->selectIndex($this->config['index_type'], true);
    $content = $app['theme']->wrapTemplateData(array(
      'post' => $response->getContent(),
      'index' => $this->renderIndex($index, $this->config['index_type'], $app),
      'title' => $this->title,
    ), $app);
    return $response->setContent(
      $app['mustache']->render('theme/layout', $content)
    );
  }

}
