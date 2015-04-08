<?php

namespace ClassCacher;

use Zend\Code\Reflection\ClassReflection;

class ClassCacher
{
    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    public function __construct()
    {
        $this->codeGenerator = new CodeGenerator();
    }

    public function generateCache(array $classes)
    {
        $return = '';
        foreach ($classes as $className) {
            $class = new ClassReflection($className);

            $classContents = $class->getContents(false);
            $classFileDir  = dirname($class->getFileName());
            $classContents = trim(str_replace('__DIR__', sprintf("'%s'", $classFileDir), $classContents));

            $return .= "namespace "
                . $class->getNamespaceName()
                . " {\n"
                . implode("\n", $this->codeGenerator->getUseLines($class))
                . $this->codeGenerator->getDeclarationLine($class) . "\n"
                . $classContents
                . "\n}\n\n";
        }

        return $return;
    }
}
