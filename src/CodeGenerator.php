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
        if (($parent = $r->getParentClass()) && $r->getNamespaceName()) {
            $parentName   = array_key_exists($parent->getName(), $usesNames)
                ? ($usesNames[$parent->getName()] ?: $parent->getShortName())
                : ((0 === strpos($parent->getName(), $r->getNamespaceName()))
                    ? substr($parent->getName(), strlen($r->getNamespaceName()) + 1)
                    : '\\' . $parent->getName());
        } else if ($parent && !$r->getNamespaceName()) {
            $parentName = '\\' . $parent->getName();
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
                return (array_key_exists($iReflection->getName(), $usesNames)
                    ? ($usesNames[$iReflection->getName()] ?: $iReflection->getShortName())
                    : ((0 === strpos($iReflection->getName(), $r->getNamespaceName()))
                        ? substr($iReflection->getName(), strlen($r->getNamespaceName()) + 1)
                        : '\\' . $iReflection->getName()));
            }, $interfaces));
        }

        return $declaration;
    }
}
