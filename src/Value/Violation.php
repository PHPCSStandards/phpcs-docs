<?php
declare(strict_types=1);

namespace App\Value;

use Assert\Assert;

class Violation
{
    private string $code;
    private string $description;
    /**
     * @var Diff[]
     */
    private array $diffs;
    private UrlList $urls;

    /**
     * @param Diff[] $diffs
     */
    public function __construct(string $code, string $description, array $diffs, UrlList $urls)
    {
        Assert::that($code)
            ->notBlank();

        Assert::thatAll($diffs)
            ->isInstanceOf(Diff::class);

        $this->code = $code;
        $this->description = $description;
        $this->diffs = $diffs;
        $this->urls = $urls;
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

    public function getUrls(): UrlList
    {
        return $this->urls;
    }
}
