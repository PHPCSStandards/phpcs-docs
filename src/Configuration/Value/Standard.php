<?php
declare(strict_types=1);

namespace App\Configuration\Value;

use Assert\Assert;

final class Standard
{
    private string $path;

    public function __construct(string $path)
    {
        Assert::that($path)
            ->notBlank();

        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
