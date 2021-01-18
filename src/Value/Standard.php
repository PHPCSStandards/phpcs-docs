<?php
declare(strict_types=1);

namespace App\Value;


use Assert\Assert;

class Standard
{
    private string $codeLocation;

    public function __construct(string $codeLocation)
    {
        Assert::that($codeLocation)
            ->endsWith('/');

        $this->codeLocation = $codeLocation;
    }

    public function getCodeLocation(): string
    {
        return $this->codeLocation;
    }
}
