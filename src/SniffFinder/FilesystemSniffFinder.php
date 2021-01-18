<?php
declare(strict_types=1);

namespace App\SniffFinder;

use App\Parser\SniffParser;
use App\Parser\ViolationParser;
use App\Value\Standard;
use GlobIterator;
use Traversable;

class FilesystemSniffFinder implements SniffFinder
{
    public function getSniffs(Standard $standard): Traversable
    {
        $parser = new SniffParser();
        $glob = new GlobIterator($standard->getCodeLocation() . 'Sniffs/*/*Sniff.php');
        foreach ($glob as $fileInfo) {
            yield $parser->parse($fileInfo->getPathname());
        }
    }

    public function getViolations(Standard $standard): Traversable
    {
        $parser = new ViolationParser();
        $glob = new GlobIterator($standard->getCodeLocation() . 'Docs/*/*Standard/*.xml');
        foreach ($glob as $fileInfo) {
            yield $parser->parse($fileInfo->getPathname());
        }
    }
}
