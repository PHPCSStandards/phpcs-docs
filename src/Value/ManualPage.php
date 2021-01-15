<?php
declare(strict_types=1);

namespace App\Value;

class ManualPage
{
    private string $ruleCode;
    private string $description;
    /**
     * @var Diff[]
     */
    private array $diffs;
    private string $docblock;
    /**
     * @var Property[]
     */
    private array $properties;
    /**
     * @var Url[]
     */
    private array $links;

    /**
     * @param Diff[] $diffs
     * @param Property[] $properties
     * @param Url[] $links
     */
    public function __construct(
        string $ruleCode,
        string $description,
        string $docblock,
        array $diffs,
        array $properties,
        array $links
    )
    {
        $this->ruleCode = $ruleCode;
        $this->description = $description;
        $this->docblock = $docblock;
        $this->diffs = $diffs;
        $this->properties = $properties;
        $this->links = $links;
    }

    public static function fromParts(XmlParts $xmlParts, PhpParts $phpParts): self
    {
        return new self(
            $xmlParts->getRuleCode(),
            $xmlParts->getDescription(),
            $phpParts->getDocblock(),
            $xmlParts->getDiffs(),
            $phpParts->getProperties(),
            array_unique(array_merge($phpParts->getLinks(), $xmlParts->getLinks())),
        );
    }

    public function getRuleCode(): string
    {
        return $this->ruleCode;
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

    /**
     * @return Url[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
