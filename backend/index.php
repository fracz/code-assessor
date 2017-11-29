<?php

namespace codeassessor;

use codeassessor\app\Application;
use codeassessor\controllers\CodeController;

require __DIR__ . '/vendor/autoload.php';
ini_set('display_errors', 'Off');
ini_set("log_errors", 1);
ini_set("error_log", Application::VAR_PATH . "/logs/error.log");

$app = new Application();
$app->group('/api', function () use ($app) {
    $app->get('/code/random', CodeController::class . ':getRandom');
});
$app->run();

