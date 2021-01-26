<?php
declare(strict_types=1);

namespace App\Configuration\Value;

final class Source
{
    private string $path;
    /**
     * @var Standard[]
     */
    private array $standards;

    /**
     * @param Standard[] $standards
     */
    public function __construct(string $path, array $standards)
    {
        $this->path = $path;
        $this->standards = $standards;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return Standard[]
     */
    public function getStandards(): array
    {
        return $this->standards;
    }
}
