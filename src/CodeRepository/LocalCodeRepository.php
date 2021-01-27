<?php
declare(strict_types=1);

namespace App\CodeRepository;

use App\Configuration\Value\Source;
use App\Value\Folder;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitCodeRepository implements CodeRepository
{
    public function downloadCode(Source $source): Folder
    {
        $localPath = new Folder(self::CODE_DOWNLOAD_PATH . $source->getLocalFolder() . '/');

        $this->runProcess($this->getCloneOrPullProcess($localPath, $source->getPath()));
        $this->runProcess($this->getComposerInstallProcess($localPath));

        return $localPath;
    }

    private function runProcess(Process $process): void
    {
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    private function getCloneOrPullProcess(Folder $localPath, string $sourcePath): Process
    {
        if (!is_dir((string)$localPath)) {
            return new Process([
                'git',
                'clone',
                $sourcePath,
                (string)$localPath
            ]);
        }

        return new Process([
            'git',
            '-C',
            (string)$localPath,
            'pull'
        ]);
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
