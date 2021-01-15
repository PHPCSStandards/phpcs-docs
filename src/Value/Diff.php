<?php
declare(strict_types=1);

namespace App\Value;

class Diff
{
    private string $before;
    private string $after;

    public function __construct(string $before, string $after)
    {
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
