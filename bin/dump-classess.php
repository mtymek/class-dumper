<?php

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../autoload.php';

$app = new \ClassDumper\DumperApp();
$app->run();
