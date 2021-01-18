<?php
declare(strict_types=1);

namespace App\Value;

class Sniff
{
    private string $code;
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
     * @var Violation[]
     */
    private array $violations;

    /**
     * @param Property[] $properties
     * @param Url[] $links
     * @param Violation[] $violations
     */
    public function __construct(string $code, string $docblock, array $properties, array $links, array $violations)
    {
        $this->code = $code;
        $this->docblock = $docblock;
        $this->properties = array_values($properties);
        $this->links = array_values($links);
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

    /**
     * @return Url[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @return Violation[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
