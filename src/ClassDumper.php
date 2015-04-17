<?php
/**
 * Class Dumper
 *
 * @link      https://github.com/mtymek/class-dumper
 * @copyright Copyright (c) 2015 Mateusz Tymek
 * @license   BSD 2-Clause
 */

namespace ClassDumper;

use Zend\Code\Reflection\ClassReflection;

class ClassDumper
{
    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    /**
     * @var array
     */
    private $cache;

    /**
     * @var array
     */
    private $cachedClasses;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->codeGenerator = new CodeGenerator();
    }

    /**
     * Returns class' code inside namespace
     *
     * @param ClassReflection $class
     */
    private function dumpClass(ClassReflection $class)
    {
        if (array_search($class->getName(), $this->cachedClasses) !== false) {
            return;
        }

        if ($class->isInternal()) {
            return;
        }

        if ($class->getParentClass()) {
            $this->dumpClass($class->getParentClass());
        }

        foreach ($class->getInterfaces() as $interface) {
            $this->dumpClass($interface);
        }

        if ($class->getTraits()) {
            foreach ($class->getTraits() as $trait) {
                $this->dumpClass($trait);
            }
        }

        $classContents = $class->getContents(false);
        $classFileDir  = dirname($class->getFileName());
        $classContents = trim(str_replace('__DIR__', sprintf("'%s'", $classFileDir), $classContents));

        $uses = implode("\n", $this->codeGenerator->getUseLines($class));

        $this->cache[] = "namespace "
            . $class->getNamespaceName()
            . " {\n"
            . ($uses ? $uses . "\n" : '')
            . $this->codeGenerator->getDeclarationLine($class) . "\n"
            . $classContents
            . "\n}\n";
        $this->cachedClasses[] = $class->getName();
    }

    /**
     * Generates merged code for specified classes
     *
     * @param array $classes
     * @return string
     */
    public function dump(array $classes)
    {
        $this->cache = [];
        $this->cachedClasses = [];

        foreach ($classes as $className) {
            $class = new ClassReflection($className);
            $this->dumpClass($class);
        }

        return implode("\n", $this->cache);
    }
}
