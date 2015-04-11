<?php

namespace ClassDumperTest;

use ClassDumper\ClassDumper;
use PHPUnit_Framework_TestCase;
use UserLib\Admin\Admin;
use UserLib\Customer\Customer;
use UserLib\Product\ProductInterface;
use UserLib\UserInterface;

class ClassDumperTest extends PHPUnit_Framework_TestCase
{
    public function testGenerateCache()
    {
        $dumper = new ClassDumper();

        $classes = [
            UserInterface::class,
            Admin::class,
            ProductInterface::class,
            Customer::class,
        ];

        $cache = $dumper->generateCache($classes);

        $this->assertEquals('namespace UserLib {
interface UserInterface
{
}
}

namespace UserLib\Admin {
use UserLib\UserInterface;class Admin implements UserInterface
{
}
}

namespace UserLib\Product {
interface ProductInterface
{
    public function getName();
}
}

namespace UserLib\Customer {
use UserLib\Admin\Admin;
use UserLib\UserInterface;
use UserLib\Product\ProductInterface;class Customer implements CustomerInterface, UserInterface
{
    public function buy(ProductInterface $product)
    {
    }

    public function sendMessageToAdmin(Admin $admin)
    {
    }
}
}

', $cache);
    }
}
