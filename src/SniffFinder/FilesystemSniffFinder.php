<?php
declare(strict_types=1);

namespace App\SniffFinder;

use App\Parser\SniffParser;
use App\Value\Folder;
use App\Value\Sniff;
use CallbackFilterIterator;
use GlobIterator;
use Iterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\SourceLocator\Type\FileIteratorSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;
use SplFileInfo;

class FilesystemSniffFinder implements SniffFinder
{
    public function getSniff(Folder $folder, string $sniffPath): Sniff
    {
        $parser = new SniffParser();
        $projectSourceLocator = $this->createProjectSourceLocator($folder);
        return $parser->parse($sniffPath, $projectSourceLocator);
    }

    public function getSniffs(Folder $folder): iterable
    {
        $parser = new SniffParser();
        $globSniffs = new GlobIterator($folder->getPath() . 'Sniffs/*/*Sniff.php');
        $projectSourceLocator = $this->createProjectSourceLocator($folder);
        foreach ($globSniffs as $fileInfo) {
            yield $parser->parse($fileInfo->getPathname(), $projectSourceLocator);
        }
    }

    /**
     * @return Iterator<SplFileInfo>
     */
    private function recursiveSearch(Folder $folder): Iterator
    {
        $dirs = new RecursiveDirectoryIterator($folder->getPath());
        $files = new RecursiveIteratorIterator($dirs);
        return new CallbackFilterIterator($files, function (SplFileInfo $fileInfo) {
            return preg_match('/\.php$/', $fileInfo->getPathname()) && !preg_match('/\/Tests\//', $fileInfo->getPathname());
        });
    }

    private function createProjectSourceLocator(Folder $folder): SourceLocator
    {
        $astLocator = (new BetterReflection())->astLocator();
        $fileInfoIterator = $this->recursiveSearch($folder);
        return new FileIteratorSourceLocator($fileInfoIterator, $astLocator);
    }
}
