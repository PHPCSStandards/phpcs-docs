<?php
declare(strict_types=1);

namespace App\Value;

use Assert\Assert;

class Url
{
    private string $url;
    private string $name;

    public function __construct(string $value)
    {
        $parts = explode(' ', $value);
        $url = array_shift($parts);
        $name = implode(' ', $parts);

        Assert::that($url)
            ->url();

        $this->url = $url;
        $this->name = $name !== '' ? $name : $this->url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->url . ($this->name !== $this->url ? ' ' . $this->name : '');
    }
}
