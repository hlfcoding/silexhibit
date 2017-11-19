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
      $this->wrapResponseContent($request, $response, $app);
    });
    return $controllers;
  }

  protected function registerServiceProviders(Application $app) {
    $app->register(new MustacheServiceProvider(), [
      'mustache.path' => $app['root'].'src/mustache/theme',
      'mustache.options' => [
        'cache' => $app['root'].'tmp/cache/mustache',
      ],
    ]); // 'mustache'
  }

  protected function renderPost(array $post, Application $app) {
    $post = $app['adapter']->conventionalPost($post);
    if (isset($post['exhibit'])) {
      foreach ($post['exhibit'] as &$media) {
        $media['url'] = $this->config['cdn_url'].'/media/'.$media['file'];
      }
    }
    $this->config = $app['config'];
    $this->config['site'] = $post['site'];
    $this->title = $post['title'];
    return $app['theme']->renderPost($post, $app);
  }

  protected function renderIndex(array $index, int $type, Application $app) {
    $index = $app['adapter']->conventionalIndex($index, ['type' => $type]);
    return $app['theme']->renderIndex($index, $type, $app);
  }

  protected function wrapResponseContent(Request $request, Response $response, Application $app) {
    $index_type = $this->config['site']['index_type'];
    $index = $app['database']->selectIndex($index_type, true);
    $content = $app['theme']->wrapTemplateData([
      'config' => $this->config,
      'index' => $this->renderIndex($index, $index_type, $app),
      'post' => $response->getContent(),
      'title' => $this->title,
      'urls' => [
        'full' => $request->getHttpHost().$request->getRequestUri(),
        'validation' => [
          'html' => 'http://validator.w3.org/check?doctype=HTML5',
          'css' => 'http://jigsaw.w3.org/css-validator/validator?profile=css3&warning=0',
        ],
      ],
    ], $app);
    return $response->setContent(
      $app['mustache']->render('layout', $content)
    );
  }

}
