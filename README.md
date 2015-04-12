Class Dumper
============

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
