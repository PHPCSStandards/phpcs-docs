<?php
declare(strict_types=1);

namespace App\Generator;

use App\Value\Diff;
use App\Value\Url;
use App\Value\UserDoc;
use Stringy\Stringy;
use function Stringy\create as s;

class MarkdownGenerator implements Generator
{
    public function __construct()
    {
    }

    public function createUserDoc(UserDoc $doc): string
    {
        return <<<MD
        # {$doc->getRuleCode()}
        
        {$doc->getDescription()}
        
        {$this->getComparisons($doc)}
        {$this->getSeeAlso($doc)}
        MD;
    }

    private function getComparisons(UserDoc $doc): string
    {
        if ($doc->getDiffs() === []) {
            return '';
        }

        $diffBlocks = implode("\n\n", $this->createDiffBlocks($doc));

        return <<<MD
        ## Comparisons
        
        {$diffBlocks}
        
        MD;
    }

    private function getSeeAlso(UserDoc $doc): string
    {
        if ($doc->getLinks() === []) {
            return '';
        }

        $links = implode("\n", $this->createLinks($doc));

        return <<<MD
        ## See Also
        
        {$links}
        
        MD;
    }

    private function prependLinesWith(string $prefix, string $lines): string
    {
        $prependedLines = array_map(function (Stringy $line) use ($prefix) {
            return (string)$line->prepend($prefix);
        }, s($lines)->lines());

        return implode("\n", $prependedLines);
    }

    /**
     * @return string[]
     */
    private function createDiffBlocks(UserDoc $doc): array
    {
        return array_map(function (Diff $diff): string {
            return <<<MD
            ```diff
            {$this->prependLinesWith('-', $diff->getBefore())}
            {$this->prependLinesWith('+', $diff->getAfter())}
            ```
            MD;
        }, $doc->getDiffs());
    }

    /**
     * @return string[]
     */
    private function createLinks(UserDoc $doc): array
    {
        return array_map(function (Url $url) {
            return "- [$url]($url)";
        }, $doc->getLinks());
    }
}
