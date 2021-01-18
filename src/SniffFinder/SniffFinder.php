<?php
declare(strict_types=1);

namespace App\SniffFinder;

use App\Value\Sniff;
use App\Value\Standard;
use App\Value\Violation;
use Traversable;

interface SniffFinder
{
    /**
     * @return Traversable<Sniff>
     */
    public function getSniffs(Standard $standard): Traversable;

    /**
     * @return Traversable<Violation>
     */
    public function getViolations(Standard $standard): Traversable;
}
