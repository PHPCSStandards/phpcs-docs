<?php
declare(strict_types=1);

namespace App\Value;

/**
 * Collection of unique URLs.
 */
class UrlList
{
    /**
     * @var Url[]
     */
    private array $urls;

    /**
     * @param Url[] $urls
     */
    public function __construct(array $urls)
    {
        $strings = array_map(function (Url $url): string {
            return (string)$url;
        }, $urls);

        $strings = array_values(array_unique($strings));

        $this->urls = array_map(function (string $url): Url {
            return new Url($url);
        }, $strings);
    }

    /**
     * @return Url[]
     */
    public function toArray(): array
    {
        return $this->urls;
    }
}
