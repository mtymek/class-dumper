<?php
/**
 * Class Dumper
 *
 * @link      https://github.com/mtymek/class-dumper
 * @copyright Copyright (c) 2015 Mateusz Tymek
 * @license   BSD 2-Clause
 */

namespace ClassDumper;

use ClassDumper\Exception\RuntimeException;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\Console;
use ZF\Console\Application;
use ZF\Console\Route;

class DumperApp extends Application
{
    public function __construct()
    {
        $routes = [[
            'name' => '<config> <output_file> [--strip]',
            'short_description' => "Generate class cache based on <config> file into <output_file>.",
            'handler' => [$this, 'generateDump'],
        ]];
        parent::__construct('Cache dumper', 1.0, $routes, Console::getInstance());
        $this->removeRoute('autocomplete');
        $this->removeRoute('help');
        $this->removeRoute('version');
    }

    public function generateDump(Route $route, AdapterInterface $console)
    {
        $configFile = $route->getMatchedParam('config');
        $outputFile = $route->getMatchedParam('output_file');
        $strip = (bool)$route->getMatchedParam('strip');

        $console->writeLine("Generating class cache from $configFile into $outputFile");

        if (!file_exists($configFile)) {
            throw new RuntimeException("Configuration file does not exist: $configFile");
        }

        $classes = include $configFile;

        if (!is_array($classes)) {
            throw new RuntimeException("Configuration file does not contain array of class names");
        }

        if (!file_exists(dirname($outputFile))) {
            mkdir(dirname($outputFile), 0777, true);
        }

        $dumper = new ClassDumper();
        $cache = $dumper->dump($classes, $strip);

        file_put_contents($outputFile, "<?php\n" . $cache);
    }
}
