<?php

use codeassessor\app\commands\CodeassManager;

require __DIR__ . '/vendor/autoload.php';
$enformManager = new CodeassManager();
$enformManager->run();
