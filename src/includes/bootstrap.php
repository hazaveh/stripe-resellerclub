<?php

use DI\Container;
use Slim\Views\Twig;

return function (Container $container) {

    define('APPLICATION', true);

    $settings = require __DIR__ . '/../../config.php';

    $container->set('settings', $settings);

    $container->set('view', function() {
        return Twig::create(__DIR__ . '/../../templates', ['cache' => false]);
    });

};