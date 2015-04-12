<?php

namespace ClassDumper;

use Zend\Code\Reflection\ClassReflection;

class CodeGenerator
{
    /**
     * @param ClassReflection $class
     * @return array
     */
    private function getUseArray(ClassReflection $class)
    {
        $usesNames = [];
        foreach ($class->getDeclaringFile()->getUses() as $use) {
            $usesNames[$use['use']] = $use['as'];
        }
        return $usesNames;
    }

    /**
     * @param ClassReflection $class
     * @return string
     */
    public function getUseLines(ClassReflection $class)
    {
        $useLines = [];
        foreach ($this->getUseArray($class) as $name => $as) {
            $useString = "use $name";
            if ($as) {
                $useString .= " as $as";
            }
            $useLines[] = $useString . ';';
        }
        return $useLines;
    }

    /**
     * @param ClassReflection $class
     * @param string $namespaceName
     * @param array $useArray
     * @return string
     */
    private function getClassNameInContext(ClassReflection $class, $namespaceName, $useArray)
    {
        if (!$namespaceName) {
            return '\\' . $class->getName();
        }
        return array_key_exists($class->getName(), $useArray)
            ? ($useArray[$class->getName()] ?: $class->getShortName())
            : ((0 === strpos($class->getName(), $namespaceName))
                ? substr($class->getName(), strlen($namespaceName) + 1)
                : '\\' . $class->getName());
    }

    /**
     * @param ClassReflection $class
     * @return string
     */
    public function getDeclarationLine(ClassReflection $class)
    {
        $usesNames = $this->getUseArray($class);

        $declaration = '';
        if ($class->isAbstract() && !$class->isInterface() && !$class->isTrait()) {
            $declaration .= 'abstract ';
        }
        if ($class->isFinal()) {
            $declaration .= 'final ';
        }
        if ($class->isInterface()) {
            $declaration .= 'interface ';
        } elseif ($class->isTrait()) {
            $declaration .= 'trait ';
        } else {
            $declaration .= 'class ';
        }
        $declaration .= $class->getShortName();
        $parentName = false;
        if ($parent = $class->getParentClass()) {
            $parentName = $this->getClassNameInContext($parent, $class->getNamespaceName(), $usesNames);
        }
        if ($parentName) {
            $declaration .= " extends {$parentName}";
        }
        $interfaces = array_diff($class->getInterfaceNames(), $parent ? $parent->getInterfaceNames() : array());
        if (count($interfaces)) {
            foreach ($interfaces as $interface) {
                $iReflection = new ClassReflection($interface);
                $interfaces  = array_diff($interfaces, $iReflection->getInterfaceNames());
            }
            $declaration .= $class->isInterface() ? ' extends ' : ' implements ';
            $declaration .= implode(', ', array_map(function($interface) use ($usesNames, $class) {
                $iReflection = new ClassReflection($interface);
                return $this->getClassNameInContext($iReflection, $class->getNamespaceName(), $usesNames);
            }, $interfaces));
        }

        return $declaration;
    }
}
