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
  protected function obfuscateEmail(string $input, int $hex_encoding = 0) {
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
    $post = $app['adapter']->conventionalPost($post);
    $this->config = $app['config'];
    $this->config['site'] = $post['site'];
    $this->title = $post['title'];
    if (isset($post['exhibit'])) {
      foreach ($post['exhibit'] as &$media) {
        $media['url'] = '/media/'.$media['file'];
        if ($app['env'] === PROD) {
          $media['url'] = $this->config['cdn_url'].$media['url'];
        }
      }
    }
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
      'config' => $this->filterConfig(),
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
    $response->setContent(
      $app['mustache']->render('layout', $content)
    );
  }

}
