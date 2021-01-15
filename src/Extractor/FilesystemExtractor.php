<?php
declare(strict_types=1);

namespace App\Extractor;

use App\Value\ManualPage;
use SplFileInfo;

class FilesystemExtractor implements Extractor
{
    public function extractManualPage(string $sniffPath): ManualPage
    {
        $phpFileInfo = new SplFileInfo($sniffPath);
        $phpParts = (new PhpParser())->getManualParts($sniffPath);

        $docsPath = str_replace('/Sniffs/', '/Docs/', $phpFileInfo->getPath());
        $xmlPath = sprintf('%s/%s.xml', $docsPath, $phpFileInfo->getBasename('.php'));
        $xmlParts = (new XmlParser())->getManualParts($xmlPath);

        return ManualPage::fromParts($xmlParts, $phpParts);
    }
}
