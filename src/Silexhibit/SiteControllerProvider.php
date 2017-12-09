<?php

namespace Silexhibit;

use Mustache\Silex\Provider\MustacheServiceProvider;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteControllerProvider implements ControllerProviderInterface {

  protected $config;
  protected $post;
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
    function(Application $app, Request $request, $section, $project) {
      $exhibit = $app['database']->selectExhibit('url', $request->getPathInfo());
      if (empty($exhibit)) { return $app->redirect('/'); }
      return $this->renderPost($exhibit, $app);
    });
    $controllers->get('/{page}/',
    function(Application $app, Request $request, $page) {
      $page = $app['database']->selectPost('url', $request->getPathInfo());
      if (empty($page)) { return $app->redirect('/'); }
      return $this->renderPost($page, $app);
    });
    $controllers->after(function(Request $request, Response $response, Application $app) {
      if ($response->isRedirect()) { return; }
      $this->wrapResponseContent($request, $response, $app);
    });
    return $controllers;
  }

  protected function filterConfig() {
    $filtered = $this->config;
    unset($filtered['db']);
    $owner = &$filtered['meta']['owner'];
    $owner['email'] = $this->obfuscateEmail($owner['email']);
    return $filtered;
  }

  // WordPress antispambot implementation.
  protected function obfuscateEmail($input, $hex_encoding = 0) {
    $output = '';
    for ($i = 0, $l = strlen($input); $i < $l; $i++) {
      $j = rand(0, 1 + $hex_encoding);
      switch ($j) {
        case 0:
          $output .= '&#'.ord($input[$i]).';';
          break;
        case 1:
          $output .= substr($input, $i, 1);
          break;
        case 2:
          $output .= '%'.sprintf('%02s', dechex(ord($input[$i])));
          break;
      }
    }
    return str_replace('@', '&#64;', $output);
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
    $this->post = $app['adapter']->conventionalPost($post);
    $this->config = $app['config'];
    $this->config['site'] = $this->post['site'];
    $this->title = $this->post['title'];
    if (isset($this->post['exhibit'])) {
      foreach ($this->post['exhibit'] as &$media) {
        $media['url'] = '/media/'.$media['file'];
        if ($app['env'] === PROD || in_array('cdn_url', $app['config']['debug'])) {
          $media['url'] = $this->config['cdn_url'].$media['url'];
        }
      }
    }
    return $app['theme']->renderPost($this->post, $app);
  }

  protected function renderIndex(array $index, $type, Application $app) {
    $index = $app['adapter']->conventionalIndex($index, ['type' => $type]);
    return $app['theme']->renderIndex($index, $type, $app);
  }

  protected function wrapResponseContent(Request $request, Response $response, Application $app) {
    $index_type = $this->config['site']['index_type'];
    $index = $app['database']->selectIndex($index_type, true);
    $content = $app['theme']->wrapTemplateData([
      'config' => $this->filterConfig(),
      'index' => $this->renderIndex($index, $index_type, $app),
      'post' => $response->getContent(),
      'title' => $this->title,
      'urls' => [
        'full' => $request->getHttpHost().$request->getRequestUri(),
      ],
    ], $app);
    $content['debug_info'] = in_array('template_data', $this->config['debug']) ? json_encode(
      array_merge($content, array('post' => $this->post)),
    JSON_PRETTY_PRINT) : null;
    $response->setContent(
      $app['mustache']->render('layout', $content)
    );
  }

}
