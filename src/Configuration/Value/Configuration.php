<?php
declare(strict_types=1);

namespace App\Configuration\Value;

use Assert\Assert;

final class Configuration
{
    private string $format;
    /**
     * @var Source[]
     */
    private array $sources;

    /**
     * @param Source[] $sources
     */
    public function __construct(string $format, array $sources)
    {
        Assert::that($format)
            ->inArray(['markdown'], sprintf('Invalid generator format "%s"', $format));

        Assert::thatAll($sources)
            ->isInstanceOf(Source::class);

        $this->format = $format;
        $this->sources = $sources;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return Source[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }
}
