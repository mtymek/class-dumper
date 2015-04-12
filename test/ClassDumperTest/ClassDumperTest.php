<?php

namespace ClassDumperTest;

use ClassDumper\ClassDumper;
use PHPUnit_Framework_TestCase;
use UserLib\Admin\Admin;
use UserLib\Admin\SuperAdmin;
use UserLib\Customer\Customer;
use UserLib\Exception\RuntimeException;
use UserLib\Product\Phone;
use UserLib\Product\ProductInterface;
use UserLib\UserInterface;

class ClassDumperTest extends PHPUnit_Framework_TestCase
{
    public function testDumpCreatesMergedClasses()
    {
        $dumper = new ClassDumper();
        $classes = [
            UserInterface::class,
            Admin::class,
            ProductInterface::class,
            Customer::class,
        ];
        $cache = $dumper->dump($classes);
        $this->assertEquals('namespace UserLib {
interface UserInterface
{
}
}

namespace UserLib\Admin {
use UserLib\UserInterface;
class Admin implements UserInterface
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
use UserLib\Product\ProductInterface as Product;
interface CustomerInterface
{
    public function buy(Product $product);

    public function sendMessageToAdmin(Admin $admin);
}
}

namespace UserLib\Customer {
use UserLib\Admin\Admin;
use UserLib\UserInterface;
use UserLib\Product\ProductInterface;
class Customer implements CustomerInterface, UserInterface
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

    public function testDumpIncludesRequiredInterfacesAndParentClasses()
    {
        $dumper = new ClassDumper();
        $classes = [
            SuperAdmin::class,
        ];
        $cache = $dumper->dump($classes);
        $this->assertEquals('namespace UserLib {
interface UserInterface
{
}
}

namespace UserLib\Admin {
use UserLib\UserInterface;
class Admin implements UserInterface
{
}
}

namespace UserLib\Admin {
interface SuperAdminInterface
{
}
}

namespace UserLib\Admin {
class SuperAdmin extends Admin implements SuperAdminInterface
{
}
}
', $cache);
    }

    public function testDumpIncludesRequiredTraits()
    {
        $dumper = new ClassDumper();
        $classes = [
            Phone::class,
        ];
        $cache = $dumper->dump($classes);
        $this->assertEquals('namespace UserLib\Product {
interface ProductInterface
{
    public function getName();
}
}

namespace UserLib\Product {
trait GpsTrait
{
    public function locate()
    {
    }
}
}

namespace UserLib\Product {
class Phone implements ProductInterface
{
    use GpsTrait;

    public function getName()
    {
        return \'phone\';
    }
}
}
', $cache);
    }

    public function testDumpSkipsInternalClasses()
    {
        $dumper = new ClassDumper();
        $classes = [
            RuntimeException::class,
        ];
        $cache = $dumper->dump($classes);
        $this->assertEquals('namespace UserLib\Exception {
class RuntimeException extends \RuntimeException
{
}
}
', $cache);
    }
}
