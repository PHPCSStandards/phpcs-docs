<?php
declare(strict_types=1);

namespace App\SniffFinder;

use App\Value\Folder;
use App\Value\Sniff;

interface SniffFinder
{
    public function getSniff(Folder $folder, string $sniffPath): Sniff;

    /**
     * @return iterable<Sniff>
     */
    public function getSniffs(Folder $folder): iterable;
}
