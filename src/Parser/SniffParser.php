<?php
declare(strict_types=1);

namespace App\Parser;

use App\Parser\Exception\NotASniffPath;
use App\Value\Diff;
use App\Value\Property;
use App\Value\Sniff;
use App\Value\Url;
use App\Value\UrlList;
use GlobIterator;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionProperty;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection as Roave;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;
use SimpleXMLElement;
use function Stringy\create as s;

class SniffParser
{
    public function parse(string $phpFilePath, SourceLocator $projectSourceLocator): Sniff
    {
        $astLocator = (new BetterReflection())->astLocator();
        $reflector = new ClassReflector(
            new AggregateSourceLocator([
                new SingleFileSourceLocator($phpFilePath, $astLocator),
                $projectSourceLocator
            ])
        );
        $classInfo = $reflector->reflect($this->getSniffClassName($phpFilePath));

        $xmlUrls = [];
        $description = '';
        $diffs = [];
        $xmlStandardPath = str_replace(['/Sniffs/', 'Sniff.php'], ['/Docs/', 'Standard.xml'], $phpFilePath);
        if (file_exists($xmlStandardPath)) {
            $xml = new SimpleXMLElement(file_get_contents($xmlStandardPath));
            $xmlUrls = $this->getXmlUrls($xml);
            $description = $this->getDescription($xml);
            $diffs = $this->getDiffs($xml);
        }

        $xmlErrorGlob = new GlobIterator(
            str_replace(['/Sniffs/', 'Sniff.php'], ['/Docs/', 'Standard/*.xml'], $phpFilePath)
        );
        $violations = [];
        foreach ($xmlErrorGlob as $fileInfo) {
            $violations[] = (new ViolationParser)->parse($fileInfo->getPathname());
        }
        return new Sniff(
            $this->getCode($phpFilePath),
            $this->getDocBlock($classInfo->getDocComment()),
            $this->getProperties($classInfo),
            $this->getUrls($classInfo, $xmlUrls),
            $description,
            $diffs,
            $violations
        );
    }

    private function getSniffClassName(string $phpFilePath): string
    {
        $parts = $this->getSniffFileParts($phpFilePath);

        return "{$parts[0]}\\Sniffs\\{$parts[1]}\\{$parts[2]}Sniff";
    }

    /**
     * @return string[]
     */
    private function getSniffFileParts(string $filePath): array
    {
        $part = '([^/]*)';
        preg_match("`$part/Sniffs/$part/{$part}Sniff\.php$`", $filePath, $matches);
        if ($matches === []) {
            throw NotASniffPath::fromPath($filePath);
        }

        return array_slice($matches, 1, 3);
    }

    /**
     * @return Url[]
     */
    private function getXmlUrls(SimpleXMLElement $xml): array
    {
        $urls = [];
        foreach ($xml->link as $link) {
            $urls[] = new Url(
                (string)s((string)$link)->trim()
            );
        }

        return $urls;
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
        $parts = $this->getSniffFileParts($filePath);

        return sprintf('%s.%s.%s', $parts[0], $parts[1], $parts[2]);
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

        /** @var Var_ $varTag */
        $varTag = $varTags[0];

        $description = $varTag->getDescription()->render();
        if ($description === '') {
            return '';
        }

        return $description;
    }

    /**
     * @param Url[] $xmlUrls
     */
    private function getUrls(ReflectionClass $classInfo, array $xmlUrls): UrlList
    {
        if ($classInfo->getDocComment() === '') {
            return new UrlList([]);
        }

        $links = DocBlockFactory::createInstance()
            ->create($classInfo->getDocComment())
            ->getTagsByName('link');

        $urls = array_map(function (string $url) {
            return new Url($url);
        }, $links);

        return new UrlList(array_merge($urls, $xmlUrls));
    }
}
