<?php
declare(strict_types=1);

namespace App\Generator;

use App\Value\Sniff;

class JekyllPageGenerator extends MarkdownGenerator implements Generator
{
    public function createSniffDoc(Sniff $sniff): string
    {
        $sniffDoc = $this->getFrontMatter($sniff) . "\n";
        $sniffDoc .= parent::createSniffDoc($sniff);

        return $sniffDoc;
    }

    private function getFrontMatter(Sniff $sniff): string
    {
        $sniffName = $sniff->getSniffName();
        if ($sniffName === '') {
            return <<<'MD'
            ---
            ---

            MD;
        }

        return <<<MD
        ---
        title: {$sniffName}
        ---

        MD;
    }
}
