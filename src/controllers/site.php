<?php

$site = $app['controllers_factory'];
$site->get('/', function () use ($app) {
  return $app['templater']->render('Hello {{planet}}', array('planet' => 'World!'));
});

return $site;
