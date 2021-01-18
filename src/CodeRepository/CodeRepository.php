<?php
declare(strict_types=1);

namespace App\CodeRepository;

use App\Value\Folder;

interface CodeRepository
{
    public const CODE_DOWNLOAD_PATH = 'var/repos/';

    public function downloadCode(string $repoName): Folder;
}
