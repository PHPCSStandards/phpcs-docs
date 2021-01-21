<?php
declare(strict_types=1);

namespace App\Value;

use Assert\Assert;

class Url
{
    private string $url;

    public function __construct(string $url)
    {
        Assert::that($url)
            ->url();

        $this->url = $url;
    }

    public function __toString()
    {
        return $this->getUrl();
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
