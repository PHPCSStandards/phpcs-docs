<?php
declare(strict_types=1);

namespace App\Value;

class PhpParts
{
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
     * @param Property[] $properties
     * @param Url[] $links
     */
    public function __construct(string $docblock, array $properties, array $links)
    {
        $this->docblock = $docblock;
        $this->properties = array_values($properties);
        $this->links = array_values($links);
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
