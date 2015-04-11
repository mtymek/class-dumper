<?php

namespace ClassDumperTest;

use ClassDumper\CodeGenerator;
use PHPUnit_Framework_TestCase;
use UserLib\Admin\SuperAdmin;
use UserLib\Customer\Customer;
use UserLib\Customer\CustomerInterface;
use UserLib\Exception\RuntimeException;
use UserLib\Product\ProductInterface;
use Zend\Code\Reflection\ClassReflection;

class CodeGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function provideDataForGetUseLines()
    {
        return [
            'three_imports' => [
                Customer::class,
                ['use UserLib\Admin\Admin;', 'use UserLib\UserInterface;', 'use UserLib\Product\ProductInterface;'],
            ],
            'no_imports' => [
                ProductInterface::class,
                [],
            ],
            'use_alias' => [
                CustomerInterface::class,
                ['use UserLib\Admin\Admin;', 'use UserLib\Product\ProductInterface as Product;'],
            ]
        ];
    }

    /**
     * @param $class
     * @param $expectedLines
     * @dataProvider provideDataForGetUseLines
     */
    public function testGetUseLines($class, $expectedLines)
    {
        $generator = new CodeGenerator();
        $lines = $generator->getUseLines(new ClassReflection($class));
        $this->assertEquals($expectedLines, $lines);
    }

    public function provideDataForGetDeclarationLine()
    {
        return [
            'two_interfaces' => [
                Customer::class,
                'class Customer implements CustomerInterface, UserInterface',
            ],
            'extend_and_implement' => [
                SuperAdmin::class,
                'class SuperAdmin extends Admin implements SuperAdminInterface',
            ],
            'fcqn' => [
                RuntimeException::class,
                'class RuntimeException extends \RuntimeException',
            ]
        ];
    }

    /**
     * @param $class
     * @param $expectedLines
     * @dataProvider provideDataForGetDeclarationLine
     */
    public function testGetDeclarationLine($class, $expectedLines)
    {
        $generator = new CodeGenerator();
        $lines = $generator->getDeclarationLine(new ClassReflection($class));
        $this->assertEquals($expectedLines, $lines);
    }
}
