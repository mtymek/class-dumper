Class Dumper
============

**Creates single file PHP containing multiple classes, to speed up application bootstrap.**

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mtymek/class-dumper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mtymek/class-dumper/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/mtymek/class-dumper/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mtymek/class-dumper/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/mtymek/class-dumper/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mtymek/class-dumper/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mtymek/class-dumper/v/stable)](https://packagist.org/packages/mtymek/class-dumper)
[![Total Downloads](https://poser.pugx.org/mtymek/class-dumper/downloads)](https://packagist.org/packages/mtymek/class-dumper)
[![License](https://poser.pugx.org/mtymek/class-dumper/license)](https://packagist.org/packages/mtymek/class-dumper)

Usage
-----

### Commandline script

First, create configuration file that lists all files you want to merge. You don't need 
to worry about class order, nor about adding required interfaces or parent classes - they 
will be added automatically to merged file. 

Config file is simple PHP file, returning array of class names:
 
```php
// config/classes-to-cache.php
return [
    // ZF2 classes
    Zend\Mvc\Application::class,
    Zend\Mvc\ApplicationInterface::class,
    Zend\EventManager\EventsCapableInterface::class,
   
    // custom classes
    Foo\Application::class,
    Foo\Listener\Auth::class    
]
```
 
Next, use `dump-classes.php` script to generate cached file: 

```bash
php ./vendor/bin/dump-classes.php config/classes-to-cache.php data/cache/classes.php.cache
```

When class cache is generated, you can include it in your application entry point: 

```php
// index.php
include 'vendor/autoload.php';
include 'data/cache/classes.php.cache';
```

You can automate generation using `composer` by adding post-install and post-update hooks
to `composer.json` file:

```json
{
    "scripts": {
        "post-install-cmd": [
            "php ./vendor/bin/dump-classes.php config/classes-to-cache.php data/cache/classes.php.cache-raw",
        ],
        "post-update-cmd": [
            "php ./vendor/bin/dump-classes.php config/classes-to-cache.php data/cache/classes.php.cache-raw",
        ]
    }
}
```

### PHP

Alternatively, cached class file can be generated in your PHP script:

```php
$dumper = new ClassDumper();
$cache = $dumper->dump([
    Foo::class,
    Bar::class,
]);
file_put_contents('data/cache/class_cache', "<?php\n" . $cache);
```

### Minifing merged file

ClassDumper can reduce size of emitted file by stripping all whitespace and comments.

It can be triggered from commandline by adding `--strip` switch:

 ```bash
php ./vendor/bin/dump-classes.php config/classes-to-cache.php classes.php.cache --strip
```
 
Using in PHP:
 
```php
$cache = $dumper->dump([ /* ... */ ], true);
```

Limitations
-----------

Not every class can be cached using `Class Dumper`. 

* class dump will end up in different directory than merged classes. Classes using constants 
like `__DIR__`or `__FILE__` will likely not work correctly.
* when using with "--strip" options, all comments - including annotations - will be stripped 
out. This will prevent annotation parser from working, if used on cached classes.

TODO
----

* throw exception when class does not exist
* warn if class contains `__DIR__` or `__FILE__` constants
* output/log statistics
* fix `__DIR__` constants
