<?php
declare(strict_types=1);

namespace App\Generator;

use App\Value\Diff;
use App\Value\Property;
use App\Value\Sniff;
use App\Value\Url;
use App\Value\UrlList;
use App\Value\Violation;
use Stringy\Stringy;
use function Stringy\create as s;

class MarkdownGenerator implements Generator
{
    public function createSniffDoc(Sniff $sniff): string
    {
        $sniffDoc = <<<MD
        # {$sniff->getCode()}
        
        {$this->getDescription($sniff)}
        {$this->getDocblock($sniff)}
        {$this->getComparisons($sniff->getDiffs())}
        {$this->getPublicProperties($sniff->getProperties())}
        {$this->getSeeAlso($sniff->getUrls())}
        {$this->getViolations($sniff->getViolations())}
        MD;

        $sniffDoc = preg_replace('/\n{3,}/', "\n\n", $sniffDoc);
        return preg_replace('/\n{2,}$/', "\n", $sniffDoc);
    }

    private function getDescription(Sniff $sniff): string
    {
        $description = $sniff->getDescription();
        if ($description === '') {
            return '';
        }

        return <<<MD
        {$description}
        
        MD;
    }

    private function getDocblock(Sniff $sniff): string
    {
        $docblock = $sniff->getDocblock();
        if ($docblock === '') {
            return '';
        }

        return <<<MD
        ## Docblock
        
        {$docblock}
        MD;
    }

    /**
     * @param Diff[] $diffs
     */
    private function getComparisons(array $diffs): string
    {
        if ($diffs === []) {
            return '';
        }

        $diffBlocks = implode("\n\n", $this->getDiffBlocks($diffs));

        return <<<MD
        ## Comparisons
        
        {$diffBlocks}
        MD;
    }

    /**
     * @param Diff[] $diffs
     * @return string[]
     */
    private function getDiffBlocks(array $diffs): array
    {
        return array_map(function (Diff $diff): string {
            return <<<MD
            ```diff
            {$this->prependLinesWith('-', $diff->getBefore())}
            {$this->prependLinesWith('+', $diff->getAfter())}
            ```
            MD;
        }, $diffs);
    }

    private function prependLinesWith(string $prefix, string $lines): string
    {
        $prependedLines = array_map(function (Stringy $line) use ($prefix) {
            return (string)$line->prepend($prefix);
        }, s($lines)->lines());

        return implode("\n", $prependedLines);
    }

    /**
     * @param Property[] $properties
     */
    private function getPublicProperties(array $properties): string
    {
        if ($properties === []) {
            return '';
        }

        $propertyLines = implode("\n", $this->getPublicPropertyLines($properties));

        return <<<MD
        ## Public Properties
        
        {$propertyLines}
        
        MD;
    }

    /**
     * @param Property[] $properties
     * @return string[]
     */
    private function getPublicPropertyLines(array $properties): array
    {
        return array_map(function (Property $property) {
            return "- `\${$property->getName()}` : {$property->getType()} {$property->getDescription()}";
        }, $properties);
    }

    /**
     * @return string
     */
    private function getSeeAlso(UrlList $urls): string
    {
        if ($urls->toArray() === []) {
            return '';
        }

        $linkLines = implode("\n", $this->getLinkLines($urls));

        return <<<MD
        ## See Also
        
        {$linkLines}
        
        MD;
    }

    /**
     * @return string[]
     */
    private function getLinkLines(UrlList $urls): array
    {
        return array_map(function (Url $url) {
            return "- [$url]($url)";
        }, $urls->toArray());
    }

    /**
     * @param Violation[] $violations
     * @return string
     */
    private function getViolations(array $violations): string
    {
        if ($violations === []) {
            return '';
        }

        $violations = implode("\n", $this->getViolationBlocks($violations));

        return <<<MD
        ## Troubleshooting
        
        {$violations}
        
        MD;
    }

    /**
     * @param Violation[] $violations
     * @return string[]
     */
    private function getViolationBlocks(array $violations): array
    {
        return array_map(function (Violation $violation): string {
            return <<<MD
            ```
            <details>
            <summary>{$violation->getCode()}</summary>
            {$this->createViolationDoc($violation)}
            </details>
            ```
            MD;
        }, $violations);
    }

    public function createViolationDoc(Violation $doc): string
    {
        return <<<MD
        {$doc->getDescription()}
        
        {$this->getComparisons($doc->getDiffs())}
        
        {$this->getSeeAlso($doc->getUrls())}
        MD;
    }
}
