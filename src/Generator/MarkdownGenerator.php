<?php
declare(strict_types=1);

namespace App\Generator;

use App\Value\Diff;
use App\Value\Property;
use App\Value\Sniff;
use App\Value\Url;
use App\Value\Violation;
use Stringy\Stringy;
use function Stringy\create as s;

class MarkdownGenerator implements Generator
{
    public function __construct()
    {
    }

    public function fromSniff(Sniff $doc): string
    {
        return <<<MD
        # {$doc->getCode()}
        
        {$doc->getDocblock()}
        
        {$this->getPublicProperties($doc->getProperties())}
        {$this->getSeeAlso($doc->getLinks())}
        {$this->getViolations($doc->getViolations())}
        MD;
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
            return "- \${$property->getName()} : {$property->getType()} {$property->getDescription()}";
        }, $properties);
    }

    /**
     * @param Url[] $links
     * @return string
     */
    private function getSeeAlso(array $links): string
    {
        if ($links === []) {
            return '';
        }

        $linkLines = implode("\n", $this->getLinkLines($links));

        return <<<MD
        ## See Also
        
        {$linkLines}
        
        MD;
    }

    /**
     * @param Url[] $links
     * @return string[]
     */
    private function getLinkLines(array $links): array
    {
        return array_map(function (Url $url) {
            return "- [$url]($url)";
        }, $links);
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
            {$this->getViolation($violation)}
            </details>
            ```
            MD;
        }, $violations);
    }

    public function getViolation(Violation $doc): string
    {
        return <<<MD
        {$doc->getDescription()}
        
        {$this->getComparisons($doc)}
        {$this->getSeeAlso($doc->getLinks())}
        MD;
    }

    private function getComparisons(Violation $doc): string
    {
        if ($doc->getDiffs() === []) {
            return '';
        }

        $diffBlocks = implode("\n\n", $this->getDiffBlocks($doc->getDiffs()));

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
}
