Class Dumper
============

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mtymek/class-dumper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mtymek/class-dumper/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/mtymek/class-dumper/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mtymek/class-dumper/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/mtymek/class-dumper/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mtymek/class-dumper/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mtymek/class-dumper/v/stable)](https://packagist.org/packages/mtymek/class-dumper)
[![Total Downloads](https://poser.pugx.org/mtymek/class-dumper/downloads)](https://packagist.org/packages/mtymek/class-dumper)
[![License](https://poser.pugx.org/mtymek/class-dumper/license)](https://packagist.org/packages/mtymek/class-dumper)

Creates single file PHP containing multiple classes, to speed up application bootstrap.

Usage
-----

```php
$dumper = new ClassDumper();
$cache = $dumper->dump([
    Foo::class,
    Bar::class,
]);
file_put_contents('data/cache/class_cache', "<?php\n" . $cache);
```

TODO
----

* throw exception when class does not exist
* strip whitespace
* fix `__DIR__` constants
* console tool
