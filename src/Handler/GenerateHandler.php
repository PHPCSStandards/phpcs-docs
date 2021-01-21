<?php
declare(strict_types=1);

namespace App\Handler;

use App\CodeRepository\CodeRepository;
use App\Generator\Generator;
use App\SniffFinder\SniffFinder;
use App\Value\Folder;
use Symfony\Component\Filesystem\Filesystem;

class GenerateHandler
{
    private CodeRepository $codeRepository;
    private Generator $generator;
    private SniffFinder $sniffFinder;

    public function __construct(CodeRepository $codeRepository, Generator $generator, SniffFinder $sniffFinder)
    {
        $this->codeRepository = $codeRepository;
        $this->generator = $generator;
        $this->sniffFinder = $sniffFinder;
    }

    /**
     * @return iterable<string>
     */
    public function handle(string $sniffPath = null): iterable
    {
        $repoName = 'PHPCompatibility/PHPCompatibility';
        $repoPath = $this->codeRepository->downloadCode($repoName);
        $filesystem = new Filesystem();

        $standardPath = new Folder($repoPath . 'PHPCompatibility/');

        if ($sniffPath !== null) {
            $sniffs = [$this->sniffFinder->getSniff($standardPath, $sniffPath)];
        } else {
            $sniffs = $this->sniffFinder->getSniffs($standardPath);
        }

        $count = count($sniffs);
        yield "Found {$count} sniff(s)";

        foreach ($sniffs as $sniff) {
            $markdownPath = $this->sniffCodeToMarkdownPath($sniff->getCode());
            $filesystem->dumpFile(
            // TODO: perhaps we can move this logic to the the sniff class
                $markdownPath,
                $this->generator->createSniffDoc($sniff)
            );
            yield "Created file: {$markdownPath}";
        }
    }

    private function sniffCodeToMarkdownPath(string $code): string
    {
        [$standard, $category, $sniff] = explode('.', $code);

        return "var/markdown/$standard/$category/$sniff.md";
    }
}
