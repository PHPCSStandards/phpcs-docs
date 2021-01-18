<?php
declare(strict_types=1);

namespace App\SniffFinder;

use App\Parser\SniffParser;
use App\Value\Folder;
use GlobIterator;
use Traversable;

class FilesystemSniffFinder implements SniffFinder
{
    public function getSniffs(Folder $folder): Traversable
    {
        $parser = new SniffParser();
        $globSniffs = new GlobIterator($folder->getPath() . 'Sniffs/*/*Sniff.php');
        foreach ($globSniffs as $fileInfo) {
            yield $parser->parse($fileInfo->getPathname());
        }
    }
}
