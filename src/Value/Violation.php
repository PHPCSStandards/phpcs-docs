<?php
declare(strict_types=1);

namespace App\Value;

class Violation
{
    private string $code;
    private string $description;
    /**
     * @var Diff[]
     */
    private array $diffs;
    /**
     * @var Url[]
     */
    private array $links;

    /**
     * @param Diff[] $diffs
     * @param Url[] $links
     */
    public function __construct(string $code, string $description, array $diffs, array $links)
    {
        $this->code = $code;
        $this->description = $description;
        $this->diffs = $diffs;
        $this->links = $links;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Diff[]
     */
    public function getDiffs(): array
    {
        return $this->diffs;
    }

    /**
     * @return Url[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
