<?php
declare(strict_types=1);

namespace App\Configuration\Value;

use App\CodeRepository\CodeRepository;
use App\Value\Folder;
use function Stringy\create as s;

final class Source
{
    public const TYPE_GIT = 'git';
    public const TYPE_LOCAL = 'local';

    private string $path;
    /**
     * @var Standard[]
     */
    private array $standards;
    private Folder $localFolder;
    private string $type = self::TYPE_LOCAL;

    /**
     * @param Standard[] $standards
     */
    public function __construct(string $path, array $standards)
    {
        $this->path = $path;
        $this->standards = $standards;

        if (preg_match('/([^.\/]+)\.git$/', $path, $matches)) {
            $this->localFolder = $this->createLocalFolder(CodeRepository::CODE_DOWNLOAD_PATH . $matches[1]);
            $this->type = self::TYPE_GIT;
            return;
        }

        $this->localFolder = $this->createLocalFolder($path);
    }

    private function createLocalFolder(string $path): Folder
    {
        $path = s($path)->ensureRight('/');
        return new Folder((string)$path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getLocalFolder(): Folder
    {
        return $this->localFolder;
    }

    /**
     * @return Standard[]
     */
    public function getStandards(): array
    {
        return $this->standards;
    }
}
