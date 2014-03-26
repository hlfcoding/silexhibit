<?php

namespace Silexhibit\Plugins\ExternalDataPlugin;

use Silex\Application;

use Guzzle\GuzzleServiceProvider,
    Guzzle\Cache\DoctrineCacheAdapter,
    Guzzle\Plugin\Cache\CachePlugin,
    Guzzle\Plugin\Cache\CallbackCanCacheStrategy,
    Guzzle\Plugin\Cache\DefaultCacheStorage,
    Guzzle\Plugin\Oauth\OauthPlugin,
    Guzzle\Plugin\Cache\RevalidationInterface,
    Guzzle\Http\Message\RequestInterface,
    Guzzle\Http\Message\Response as GuzzleResponse;

use Doctrine\Common\Cache\FilesystemCache,
    Doctrine\Common\Inflector\Inflector;

use Symfony\Component\HttpFoundation\Response;

use Silexhibit\Plugin,
    Silexhibit\Controller,
    Silexhibit\Plugin\PluggableViewInterface;

class ExternalDataPlugin extends Plugin implements RevalidationInterface
{
  protected $services;

  protected $guzzle;
  protected $guzzle_client;

  protected $cache_path;

  public function __construct()
  {
    parent::__construct('external-data');
  }
  public function register(Application $app, $context)
  {
    parent::register($app, $context);
    if (!isset($this->config) || !isset($this->config['services'])) {
      throw new \Exception("Plugin configuration required.", 1);
    }
    $this->services = $this->config['services'];
    if ($context instanceof Controller) {
      if (!isset($app['guzzle'])) {
        $app->register(new GuzzleServiceProvider, array());
      }
      $this->guzzle = $app['guzzle'];
      $this->guzzle_client = $app['guzzle.client'];
      if (!isset($this->config['options'])) {
        $this->config['options'] = array(
          'should_cache' => true,
        );
      }
      if ($this->config['options']['should_cache']) {
        $this->cache_path = $app['path.cache']($this->name);
      }
    }
    $this->context = $context;
  }
  public function run($opts, $context=null)
  {
    $result = parent::run($opts, $context);
    if ($result !== false) {
      return $result;
    }
    $context = isset($context) ?: $this->context;
    if ($context instanceof Controller) {
      $controller = $context;
      $service_name = $opts['plugin_endpoint'];
      if (!isset($this->services[$service_name])) {
        // TODO: Invalid.
      }
      $service = $this->services[$service_name];
      $client = $this->guzzle_client;
      $config = $client->getConfig();
      $client->setBaseUrl($service['base_url']);
      $endpoint = $service['endpoint'];
      $format = 'json';
      if (isset($service['format'])) {
        $format = $service['format'];
        $endpoint .= ".$format";
      }
      $headers = array();
      $r_opts = array();
      if (isset($service['params'])) {
        $r_opts['query'] = $service['params'];
      }
      if ($service_name === 'twitter') {
        $client->addSubscriber(new OauthPlugin($service['oauth']));
      }
      if ($this->config['options']['should_cache']) {
        if (!isset($service['hours'])) {
          $service['hours'] = 1;
        }
        $config['params.cache.override_ttl'] = $service['hours'] * 3600;
        $config['params.cache.revalidate'] = 'skip';
        $cache = new CachePlugin(array(
          'storage' => new DefaultCacheStorage(
            new DoctrineCacheAdapter(
              new FilesystemCache($this->cache_path, $format)
            )
          ),
          'revalidation' => $this,
          'can_cache' => new CallbackCanCacheStrategy(null,
            function ($response) { return $response->isSuccessful(); }
          ),
        ));
        $client->addSubscriber($cache);
      }
      $client->setConfig($config);
      $request = $client->get($endpoint, $headers, $r_opts);
      $response = $request->send();
      switch ($format) {
        case 'atom':
        case 'xml': $content_type = 'application/xml'; break;
        default: $content_type = 'application/json'; break;
      }
      $headers = array_merge(
        $response->getHeaders()->toArray(),
        array('Content-Type' => $content_type)
      );
      return new Response(
        $response->getBody(),
        $response->getStatusCode(),
        $headers
      );
    }
    return false;
  }
  protected function provideContent(PluggableViewInterface $view)
  {
    $formats = array();
    $orders = array();
    $units = array();
    foreach ($this->services as $name => $s) {
      $formats[$name] = isset($s['format']) ? $s['format'] : 'json';
      $orders[$name] = isset($s['order']) ? $s['order'] : null;
      $units[$name] = isset($s['unit']) ? $s['unit'] : 'item';
    }
    $formats = json_encode($formats);
    $orders = json_encode($orders);
    $units = json_encode($units);
    $options = array();
    foreach ($this->config['options'] as $key => $value) {
      $options[Inflector::camelize($key)] = $value;
    }
    $options = json_encode($options);
    $view->addPluginSnippet(
      "Silexhibit.ExternalData.FEED_FORMATS = $formats;\n".
      "Silexhibit.ExternalData.FEED_ORDERS = $orders;\n".
      "Silexhibit.ExternalData.FEED_UNITS = $units;\n".
      "jQuery.extend(Silexhibit.ExternalData.options, $options);"
    );
    return null;
  }

  public function revalidate(RequestInterface $request, GuzzleResponse $response)
  {
    return true;
  }
  public function shouldRevalidate(RequestInterface $request, GuzzleResponse $response)
  {
    return false;
  }

}
