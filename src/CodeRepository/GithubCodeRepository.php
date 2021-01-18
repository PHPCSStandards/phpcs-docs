<?php
declare(strict_types=1);

namespace App\CodeRepository;

use App\Value\Folder;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GithubCodeRepository implements CodeRepository
{
    public function downloadCode(string $repoName): Folder
    {
        $repoPath = new Folder(self::CODE_DOWNLOAD_PATH . '/' . $repoName . '/');

        $process = $this->cloneOrPull($repoPath, $repoName);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $repoPath;
    }

    private function cloneOrPull(Folder $repoPath, string $repoName): Process
    {
        if (!is_dir((string)$repoPath)) {
            return new Process([
                'git',
                'clone',
                'git@github.com:' . $repoName,
                $repoPath
            ]);
        }

        return new Process([
            'git',
            '-C',
            $repoPath,
            'pull'
        ]);
    }
}
