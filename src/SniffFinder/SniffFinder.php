<?php
declare(strict_types=1);

namespace App\SniffFinder;

use App\Value\Folder;
use App\Value\Sniff;
use Traversable;

interface SniffFinder
{
    /**
     * @return Traversable<Sniff>
     */
    public function getSniffs(Folder $folder): Traversable;
}
