<?php
declare(strict_types=1);

namespace App\CodeRepository;

use App\Configuration\Value\Source;
use App\Value\Folder;

interface CodeRepository
{
    public const CODE_DOWNLOAD_PATH = 'var/repos/';

    public function getFolder(Source $source): Folder;
}
