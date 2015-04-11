<?php

namespace ClassDumper;

use Zend\Code\Reflection\ClassReflection;

class ClassDumper
{
    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    private $cache;

    private $cachedClasses;

    public function __construct()
    {
        $this->codeGenerator = new CodeGenerator();
    }

    private function dumpClass(ClassReflection $class)
    {
        if (array_search($class->getName(), $this->cachedClasses) !== false) {
            return;
        }

        if ($class->getParentClass()) {
            $this->dumpClass($class->getParentClass());
        }

        foreach ($class->getInterfaces() as $interface) {
            $this->dumpClass($interface);
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
