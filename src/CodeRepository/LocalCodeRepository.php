<?php
declare(strict_types=1);

namespace App\CodeRepository;

use App\Configuration\Value\Source;
use App\Value\Folder;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class LocalCodeRepository implements CodeRepository
{
    public function getFolder(Source $source): Folder
    {
        $this->runProcess($this->getComposerInstallProcess($source->getLocalFolder()));

        return $source->getLocalFolder();
    }

    private function runProcess(Process $process): void
    {
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    private function getComposerInstallProcess(Folder $repoPath): Process
    {
        return new Process([
            'composer',
            'install',
            '-d',
            $repoPath
        ]);
    }
}
