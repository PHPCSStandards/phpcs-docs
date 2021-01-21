<?php
declare(strict_types=1);

namespace App\Value;

use Assert\Assert;

class Sniff
{
    private string $code;
    private string $docblock;
    /**
     * @var Property[]
     */
    private array $properties;
    private UrlList $urls;
    private string $description;
    /**
     * @var Diff[]
     */
    private array $diffs;
    /**
     * @var Violation[]
     */
    private array $violations;

    /**
     * @param Property[] $properties
     * @param Diff[] $diffs
     * @param Violation[] $violations
     */
    public function __construct(
        string $code,
        string $docblock,
        array $properties,
        UrlList $urls,
        string $description,
        array $diffs,
        array $violations
    )
    {
        Assert::that($code)
            ->notBlank();

        Assert::thatAll($properties)
            ->isInstanceOf(Property::class);

        Assert::thatAll($diffs)
            ->isInstanceOf(Diff::class);

        Assert::thatAll($violations)
            ->isInstanceOf(Violation::class);

        $this->code = $code;
        $this->docblock = $docblock;
        $this->properties = array_values($properties);
        $this->urls = $urls;
        $this->description = $description;
        $this->diffs = $diffs;
        $this->violations = $violations;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDocblock(): string
    {
        return $this->docblock;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getUrls(): UrlList
    {
        return $this->urls;
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

    /**
     * @return Violation[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
