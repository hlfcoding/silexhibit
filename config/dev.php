<?php

$common = require_once __DIR__.'/common.php';

return array_merge_recursive($common, [
  'debug' => ['template_data'],
  'db' => [
    'host' => '127.0.0.1',
    'password' => '',
    'user' => 'root',
  ],
]);
