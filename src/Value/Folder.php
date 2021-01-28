<?php
declare(strict_types=1);

namespace App\Value;

use Assert\Assert;

final class Folder
{
    private string $path;

    public function __construct(string $path)
    {
        Assert::that($path)
            ->endsWith('/');

        $this->path = $path;
    }

    public function __toString(): string
    {
        return $this->getPath();
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
