<?php
declare(strict_types=1);

namespace App\Parser;

use App\Value\Diff;
use App\Value\Url;
use App\Value\XmlParts;
use SimpleXMLElement;
use Stringy\Stringy as s;

class UserDocParser
{
    public function getManualParts(string $filePath): XmlParts
    {
        $doc = new SimpleXMLElement(file_get_contents($filePath));

        return new XmlParts(
            $this->getRuleCode($doc),
            $this->getDescription($doc),
            $this->getDiffs($doc),
            $this->getLinks($doc)
        );
    }

    private function getRuleCode(SimpleXMLElement $doc): string
    {
        return (string)s::create((string)$doc->rule_code)->trim();
    }

    private function getDescription(SimpleXMLElement $doc): string
    {
        return (string)s::create((string)$doc->standard)->trim();
    }

    /**
     * @return Diff[]
     */
    private function getDiffs(SimpleXMLElement $doc): array
    {
        $comparisons = [];
        foreach ($doc->code_comparison as $comparison) {
            $comparisons[] = new Diff(
                (string)s::create((string)$comparison->code[0])->trim(),
                (string)s::create((string)$comparison->code[1])->trim(),
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
                (string)s::create((string)$link)->trim()
            );
        }

        return $links;
    }
}
