<?php
declare(strict_types=1);

namespace App\Generator;

use App\Value\Sniff;
use App\Value\Violation;

interface Generator
{
    public function getViolation(Violation $doc): string;

    public function createSniffDoc(Sniff $sniff): string;
}
