<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MiniLink\MiniLink;

$app = new MiniLink();
$app->handleRequest();
