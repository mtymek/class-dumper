<?php

namespace ClassDumper;

use Zend\Code\Generator\ValueGenerator;
use Zend\Code\Reflection\ClassReflection;

class ConfigGenerator
{
    private $skip = [
        'ComposerAutoloaderInit',
        'ClassDumper\\',
    ];

    private function shouldSkip(ClassReflection $class)
    {
        if ($class->isInternal()) {
            return true;
        }
        foreach ($this->skip as $prefix) {
            if (strpos($class->getName(), $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    private function generateConfig()
    {
        $allClasses = get_declared_classes();
        $classList = [];

        foreach ($allClasses as $class) {
            $class = new ClassReflection($class);

            if ($this->shouldSkip($class)) {
                continue;
            }

            $classList[] = $class->getName();
        }

        $generator = new ValueGenerator($classList);
        return $generator->generate();
    }

    public function dumpIncludedClasses($fileName)
    {
        $config = $this->generateConfig();

        file_put_contents(
            $fileName,
            "<?php\n\nreturn " . $config . ";\n"
        );
    }
}
