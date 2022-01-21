<?php
declare(strict_types=1);

namespace App\Generator;

use App\Generator\Formatter\MarkdownFormatter;
use App\Value\Sniff;
use App\Value\Violation;

final class MarkdownGenerator implements Generator
{
    private MarkdownFormatter $formatter;
    private const LINE_ENDING_REGEX = '\r\n|\n';

    public function __construct(MarkdownFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function createSniffDoc(Sniff $sniff): string
    {
        $sniffDoc = <<<MD
        # {$sniff->getCode()}
        
        {$this->formatter->formatDescription($sniff)}
        
        {$this->formatter->formatDocblock($sniff)}
        
        {$this->formatter->formatComparisons($sniff->getDiffs())}
        
        {$this->formatter->formatPublicProperties($sniff->getProperties())}
        
        {$this->formatter->formatSeeAlso($sniff->getUrls())}
        
        {$this->formatter->formatViolations($sniff->getViolations())}
        MD;

        $sniffDoc = preg_replace('`'.self::LINE_ENDING_REGEX.'{3,}`', "\n\n", $sniffDoc);
        return preg_replace('`'.self::LINE_ENDING_REGEX.'{2,}$`', "\n", $sniffDoc);
    }

    public function createViolationDoc(Violation $violation): string
    {
        return <<<MD
        {$violation->getDescription()}
        
        {$this->formatter->formatComparisons($violation->getDiffs())}
        
        {$this->formatter->formatSeeAlso($violation->getUrls())}
        MD;
    }
}
