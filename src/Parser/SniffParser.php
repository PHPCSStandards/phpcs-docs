<?php
declare(strict_types=1);

namespace App\Parser;

use App\Parser\Exception\NotASniffPath;
use App\Value\Diff;
use App\Value\Property;
use App\Value\Sniff;
use App\Value\Url;
use App\Value\Urls;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionProperty;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection as Roave;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use SimpleXMLElement;
use function Stringy\create as s;

class SniffParser
{
    public function parse(string $phpFilePath): Sniff
    {
        $astLocator = (new BetterReflection())->astLocator();
        $reflector = new ClassReflector(new SingleFileSourceLocator($phpFilePath, $astLocator));
        $classInfo = $reflector->getAllClasses()[0];

        $xmlUrls = [];
        $description = '';
        $diffs = [];
        $xmlFilePath = str_replace(['/Sniffs/', '.php'], ['/Docs/', '.xml'], $phpFilePath);
        if (file_exists($xmlFilePath)) {
            $xml = new SimpleXMLElement(file_get_contents($xmlFilePath));
            $xmlUrls = $this->getXmlUrls($xml);
            $description = $this->getDescription($xml);
            $diffs = $this->getDiffs($xml);
        }

        return new Sniff(
            $this->getCode($phpFilePath),
            $this->getDocBlock($classInfo->getDocComment()),
            $this->getProperties($classInfo),
            $this->getLinks($classInfo, $xmlUrls),
            $description,
            $diffs,
            []
        );
    }

    /**
     * @return Url[]
     */
    private function getXmlUrls(SimpleXMLElement $xml): array
    {
        $links = [];
        foreach ($xml->link as $link) {
            $links[] = new Url(
                (string)s((string)$link)->trim()
            );
        }

        return $links;
    }

    private function getDescription(SimpleXMLElement $xml): string
    {
        return (string)s((string)$xml->standard)->trim();
    }

    /**
     * @return Diff[]
     */
    private function getDiffs(SimpleXMLElement $xml): array
    {
        $comparisons = [];
        foreach ($xml->code_comparison as $comparison) {
            $comparisons[] = new Diff(
                (string)s((string)$comparison->code[1])->trim(),
                (string)s((string)$comparison->code[0])->trim(),
            );
        }

        return $comparisons;
    }

    private function getCode(string $filePath): string
    {
        $part = '([^\/]*)';
        preg_match("/$part\/Sniffs\/$part\/$part.php/", $filePath, $matches);
        if ($matches === []) {
            throw NotASniffPath::fromPath($filePath);
        }

        return sprintf('%s.%s.%s', $matches[1], $matches[2], $matches[3]);
    }

    private function getDocBlock(string $docComment): string
    {
        if ($docComment === '') {
            return '';
        }

        $docBlock = DocBlockFactory::createInstance()
            ->create($docComment);

        $docBlockSummary = $docBlock->getSummary();
        $docBlockDescription = (string)$docBlock->getDescription();

        return $docBlockSummary . ($docBlockDescription !== '' ? "\n\n" . $docBlock->getDescription() : '');
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

            return new Property(
                $property->getName(),
                implode('|', array_unique($types)),
                $this->getPropertyDescription($property)
            );
        }, $properties);
    }

    private function getPropertyDescription(Roave\ReflectionProperty $property): string
    {
        $docComment = $property->getDocComment();
        if ($docComment === '') {
            return '';
        }

        $varTags = DocBlockFactory::createInstance()->create($docComment)->getTagsByName('var');
        if ($varTags === []) {
            return '';
        }

        $varTag = $varTags[0];
        if (!$varTag instanceof Var_) {
            return '';
        }

        $description = $varTag->getDescription()->render();
        if ($description === '') {
            return '';
        }

        return $description;
    }

    /**
     * @param Url[] $xmlUrls
     */
    private function getLinks(ReflectionClass $classInfo, array $xmlUrls): Urls
    {
        if ($classInfo->getDocComment() === '') {
            return new Urls([]);
        }

        $links = DocBlockFactory::createInstance()
            ->create($classInfo->getDocComment())
            ->getTagsByName('link');

        $urls = array_map(function (string $url) {
            return new Url($url);
        }, $links);

        return new Urls(array_merge($urls, $xmlUrls));
    }
}
