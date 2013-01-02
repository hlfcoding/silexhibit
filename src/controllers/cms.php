<?php

$cms = $app['controllers_factory'];
$cms->get('/', function () use ($app) { return 'Home page'; });

return $cms;
