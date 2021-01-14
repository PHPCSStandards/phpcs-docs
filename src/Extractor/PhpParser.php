<?php
declare(strict_types=1);

namespace App\Extractor;

use App\Value\PhpParts;
use App\Value\Property;
use App\Value\Url;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionProperty;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use Roave\BetterReflection\Reflection as Roave;

class PhpParser
{
    public function getManualParts(string $filePath): PhpParts
    {
        $astLocator = (new BetterReflection())->astLocator();
        $reflector = new ClassReflector(new SingleFileSourceLocator($filePath, $astLocator));
        $classInfo = $reflector->getAllClasses()[0];

        return new PhpParts(
            $this->getDocBlock($classInfo),
            $this->getProperties($classInfo),
            $this->getLinks($classInfo)
        );
    }

    private function getDocBlock(ReflectionClass $classInfo): string
    {
        if ($classInfo->getDocComment() === '') {
            return '';
        }

        return DocBlockFactory::createInstance()
            ->create($classInfo->getDocComment())
            ->getSummary();
    }

    /**
     * @return Property[]
     */
    private function getProperties(ReflectionClass $classInfo): array
    {
        $properties = $classInfo->getProperties(ReflectionProperty::IS_PUBLIC);

        return array_map(function (Roave\ReflectionProperty $property) {
            $types = $property->getDocBlockTypeStrings();
            $propertyType = $property->getType();
            if ($propertyType !== null) {
                $types[] = (string)$property->getType();
            }
            if ($types === []) {
                $types[] = 'mixed';
            }
            return new Property($property->getName(), implode('|', array_unique($types)));
        }, $properties);
    }

    /**
     * @return Url[]
     */
    private function getLinks(ReflectionClass $classInfo): array
    {
        if ($classInfo->getDocComment() === '') {
            return [];
        }

        $links = DocBlockFactory::createInstance()
            ->create($classInfo->getDocComment())
            ->getTagsByName('link');

        return array_map(function (string $url) {
            return new Url($url);
        }, $links);
    }
}
