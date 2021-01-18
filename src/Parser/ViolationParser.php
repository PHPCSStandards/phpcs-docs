<?php
declare(strict_types=1);

namespace App\Parser;

use App\Parser\Exception\NotAViolationPath;
use App\Value\Diff;
use App\Value\Url;
use App\Value\Violation;
use SimpleXMLElement;
use function Stringy\create as s;

class ViolationParser
{
    public function parse(string $filePath): Violation
    {
        $doc = new SimpleXMLElement(file_get_contents($filePath));

        return new Violation(
            $this->getErrorCode($filePath),
            $this->getDescription($doc),
            $this->getDiffs($doc),
            $this->getLinks($doc)
        );
    }

    private function getErrorCode(string $filePath): string
    {
        $part = '([^\/]*)';
        preg_match("/$part\/Docs\/$part\/$part\/$part.xml/", $filePath, $matches);
        if ($matches === []) {
            throw NotAViolationPath::fromPath($filePath);
        }

        return sprintf('%s.%s.%s.%s', $matches[1], $matches[2], $matches[3], $matches[4]);
    }

    private function getDescription(SimpleXMLElement $doc): string
    {
        return (string)s((string)$doc->standard)->trim();
    }

    /**
     * @return Diff[]
     */
    private function getDiffs(SimpleXMLElement $doc): array
    {
        $comparisons = [];
        foreach ($doc->code_comparison as $comparison) {
            $comparisons[] = new Diff(
                (string)s((string)$comparison->code[1])->trim(),
                (string)s((string)$comparison->code[0])->trim(),
            );
        }

        return $comparisons;
    }

    /**
     * @return Url[]
     */
    private function getLinks(SimpleXMLElement $doc): array
    {
        if (count($doc->links) === 0) {
            return [];
        }

        $links = [];
        foreach ($doc->links[0]->link as $link) {
            $links[] = new Url(
                (string)s((string)$link)->trim()
            );
        }

        return $links;
    }
}
