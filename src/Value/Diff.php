<?php
declare(strict_types=1);

namespace App\Value;

use Assert\Assert;

class Diff
{
    private string $before;
    private string $after;

    public function __construct(string $before, string $after)
    {
        Assert::thatAll([$before, $after])
            ->notBlank();

        $this->before = $before;
        $this->after = $after;
    }

    public function getBefore(): string
    {
        return $this->before;
    }

    public function getAfter(): string
    {
        return $this->after;
    }
}
