<?php
declare(strict_types=1);

namespace App\Value;

use Assert\Assert;

class Sniff
{
    private string $code;
    private string $standardName;
    private string $categoryName;
    private string $sniffName;
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

        $sniffNameParts = explode('.', $code);
        Assert::that($sniffNameParts)
            ->isArray()
            ->count(3);

        Assert::thatAll($properties)
            ->isInstanceOf(Property::class);

        Assert::thatAll($diffs)
            ->isInstanceOf(Diff::class);

        Assert::thatAll($violations)
            ->isInstanceOf(Violation::class);

        $this->code = $code;
        $this->standardName = $sniffNameParts[0];
        $this->categoryName = $sniffNameParts[1];
        $this->sniffName = $sniffNameParts[2];
        $this->docblock = $docblock;
        $this->properties = array_values($properties);
        $this->urls = $urls;
        $this->description = $description;
        $this->diffs = $diffs;
        $this->violations = $violations;
    }

    /** @return non-empty-string */
    public function getCode(): string
    {
        return $this->code;
    }

    /** @return non-empty-string */
    public function getStandardName(): string
    {
        return $this->standardName;
    }

    /** @return non-empty-string */
    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    /** @return non-empty-string */
    public function getSniffName(): string
    {
        return $this->sniffName;
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

    public function withCode(string $newValue): self
    {
        return new self(
            $newValue,
            $this->docblock,
            $this->properties,
            $this->urls,
            $this->description,
            $this->diffs,
            $this->violations
        );
    }

    public function withDocblock(string $newValue): self
    {
        return new self(
            $this->code,
            $newValue,
            $this->properties,
            $this->urls,
            $this->description,
            $this->diffs,
            $this->violations
        );
    }

    /**
     * @param Property[] $newValue
     */
    public function withProperties(array $newValue): self
    {
        return new self(
            $this->code,
            $this->docblock,
            $newValue,
            $this->urls,
            $this->description,
            $this->diffs,
            $this->violations
        );
    }

    public function withUrls(UrlList $newValue): self
    {
        return new self(
            $this->code,
            $this->docblock,
            $this->properties,
            $newValue,
            $this->description,
            $this->diffs,
            $this->violations
        );
    }

    public function withDescription(string $newValue): self
    {
        return new self(
            $this->code,
            $this->docblock,
            $this->properties,
            $this->urls,
            $newValue,
            $this->diffs,
            $this->violations
        );
    }

    /**
     * @param Diff[] $newValue
     */
    public function withDiffs(array $newValue): self
    {
        return new self(
            $this->code,
            $this->docblock,
            $this->properties,
            $this->urls,
            $this->description,
            $newValue,
            $this->violations
        );
    }

    /**
     * @param Violation[] $newValue
     */
    public function withViolations(array $newValue): self
    {
        return new self(
            $this->code,
            $this->docblock,
            $this->properties,
            $this->urls,
            $this->description,
            $this->diffs,
            $newValue
        );
    }
}
