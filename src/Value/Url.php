<?php
declare(strict_types=1);

namespace App\Value;

class Url
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function __toString()
    {
        return $this->getUrl();
    }
}
