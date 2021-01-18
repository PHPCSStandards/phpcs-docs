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
    private Urls $links;

    /**
     * @param Diff[] $diffs
     */
    public function __construct(string $code, string $description, array $diffs, Urls $links)
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

    public function getLinks(): Urls
    {
        return $this->links;
    }
}
