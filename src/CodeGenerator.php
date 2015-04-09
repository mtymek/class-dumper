<?php

namespace ClassCacher;

use Zend\Code\Reflection\ClassReflection;

class CodeGenerator
{
    /**
     * @param ClassReflection $r
     * @return array
     */
    private function getUseArray(ClassReflection $r)
    {
        $usesNames = [];
        foreach ($r->getDeclaringFile()->getUses() as $use) {
            $usesNames[$use['use']] = $use['as'];
        }
        return $usesNames;
    }

    /**
     * @param ClassReflection $r
     * @return string
     */
    public function getUseLines(ClassReflection $r)
    {
        $useLines = [];
        foreach ($this->getUseArray($r) as $name => $as) {
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

    public function getDeclarationLine(ClassReflection $r)
    {
        $usesNames = $this->getUseArray($r);

        $declaration = '';
        if ($r->isAbstract() && !$r->isInterface()) {
            $declaration .= 'abstract ';
        }
        if ($r->isFinal()) {
            $declaration .= 'final ';
        }
        if ($r->isInterface()) {
            $declaration .= 'interface ';
        } elseif ($r->isTrait()) {
            $declaration .= 'trait ';
        } else {
            $declaration .= 'class ';
        }
        $declaration .= $r->getShortName();
        $parentName = false;
        if ($parent = $r->getParentClass()) {
            $parentName = $this->getClassNameInContext($parent, $r->getNamespaceName(), $usesNames);
        }
        if ($parentName) {
            $declaration .= " extends {$parentName}";
        }
        $interfaces = array_diff($r->getInterfaceNames(), $parent ? $parent->getInterfaceNames() : array());
        if (count($interfaces)) {
            foreach ($interfaces as $interface) {
                $iReflection = new ClassReflection($interface);
                $interfaces  = array_diff($interfaces, $iReflection->getInterfaceNames());
            }
            $declaration .= $r->isInterface() ? ' extends ' : ' implements ';
            $declaration .= implode(', ', array_map(function($interface) use ($usesNames, $r) {
                $iReflection = new ClassReflection($interface);
                return $this->getClassNameInContext($iReflection, $r->getNamespaceName(), $usesNames);
            }, $interfaces));
        }

        return $declaration;
    }
}
