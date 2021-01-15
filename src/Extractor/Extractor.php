<?php
declare(strict_types=1);

namespace App\Extractor;

use App\Value\ManualPage;

interface Extractor
{
    public function extractManualPage(string $sniffPath): ManualPage;
}
