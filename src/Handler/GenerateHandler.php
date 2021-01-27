<?php
declare(strict_types=1);

namespace App\Handler;

use App\CodeRepository\CodeRepository;
use App\Configuration\ConfigurationRepository;
use App\Generator\Generator;
use App\SniffFinder\SniffFinder;
use App\Value\Folder;
use Symfony\Component\Filesystem\Filesystem;

class GenerateHandler
{
    private CodeRepository $codeRepository;
    private Generator $generator;
    private SniffFinder $sniffFinder;
    private ConfigurationRepository $configRepo;

    public function __construct(
        CodeRepository $codeRepository,
        Generator $generator,
        SniffFinder $sniffFinder,
        ConfigurationRepository $configRepo
    )
    {
        $this->codeRepository = $codeRepository;
        $this->generator = $generator;
        $this->sniffFinder = $sniffFinder;
        $this->configRepo = $configRepo;
    }

    /**
     * @return iterable<string>
     */
    public function handle(string $sniffPath = null): iterable
    {
        $config = $this->configRepo->getConfig();
        $filesystem = new Filesystem();

        foreach ($config->getSources() as $source) {
            $repoPath = $this->codeRepository->getFolder($source);
            foreach ($source->getStandards() as $standard) {
                $standardFolder = new Folder($repoPath . $standard->getPath() . '/');
                yield "Searching for sniffs in {$standardFolder}...";

                if ($sniffPath !== null) {
                    $sniffs = [$this->sniffFinder->getSniff($standardFolder, $sniffPath)];
                } else {
                    $sniffs = $this->sniffFinder->getSniffs($standardFolder);
                }

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
        }
    }

    private function sniffCodeToMarkdownPath(string $code): string
    {
        [$standard, $category, $sniff] = explode('.', $code);

        return "var/markdown/$standard/$category/$sniff.md";
    }
}
