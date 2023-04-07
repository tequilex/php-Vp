<?php

use App\Controller\Login;
use Base\Application;
use Base\Route;

require './src/config.php';
require './vendor/autoload.php';

$route = new Route();
$route->add('/', Login::class);

$app = new Application($route);
$app->run();