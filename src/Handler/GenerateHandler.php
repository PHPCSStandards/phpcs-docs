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

    public function handle()
    {
        $repoName = 'PHPCompatibility/PHPCompatibility';
        $repoPath = $this->codeRepository->downloadCode($repoName);
        $filesystem = new Filesystem();

        $standardPath = new Folder($repoPath . 'PHPCompatibility/');

        foreach ($this->sniffFinder->getSniffs($standardPath) as $sniff) {
            $filesystem->dumpFile(
            // TODO: perhaps we can move this logic to the the sniff class
                $this->sniffCodeToMarkdownPath($sniff->getCode()),
                $this->generator->createSniffDoc($sniff)
            );
        }
    }

    private function sniffCodeToMarkdownPath(string $code): string
    {
        [$standard, $category, $sniff] = explode('.', $code);

        return "var/markdown/$standard/$category/$sniff.md";
    }
}
