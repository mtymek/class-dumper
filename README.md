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
```

TODO
----

* handle traits
* strip whitespace
* fix `__DIR__` constants
