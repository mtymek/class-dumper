#!/usr/bin/env php
<?php
/**
 * Class Dumper
 *
 * @link      https://github.com/mtymek/class-dumper
 * @copyright Copyright (c) 2015 Mateusz Tymek
 * @license   BSD 2-Clause
 */

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

$app = new \ClassDumper\DumperApp();
$app->run();
