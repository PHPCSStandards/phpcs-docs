<?php
declare(strict_types=1);

namespace App\Parser;

use App\Parser\Exception\NotAViolationPath;
use App\Value\Diff;
use App\Value\Url;
use App\Value\UrlList;
use App\Value\Violation;
use SimpleXMLElement;
use function Stringy\create as s;

class ViolationParser
{
    public function parse(string $xmlFilePath): Violation
    {
        $xml = new SimpleXMLElement(file_get_contents($xmlFilePath));

        return new Violation(
            $this->getErrorCode($xmlFilePath),
            $this->getDescription($xml),
            $this->getDiffs($xml),
            $this->getUrls($xml)
        );
    }

    private function getErrorCode(string $xmlFilePath): string
    {
        $part = '([^\/]*)';
        preg_match("/$part\/Docs\/$part\/{$part}Standard\/$part\.xml/", $xmlFilePath, $matches);
        if ($matches === []) {
            throw NotAViolationPath::fromPath($xmlFilePath);
        }

        return sprintf('%s.%s.%s.%s', $matches[1], $matches[2], $matches[3], $matches[4]);
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

    private function getUrls(SimpleXMLElement $xml): UrlList
    {
        $urls = [];
        foreach ($xml->link as $link) {
            $urls[] = new Url(
                (string)s((string)$link)->trim()
            );
        }

        return new UrlList($urls);
    }
}
