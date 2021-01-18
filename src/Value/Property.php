<?php
declare(strict_types=1);

namespace App\Value;

class Property
{
    private string $name;
    private string $type;
    private string $description;

    public function __construct(string $name, string $type, string $description)
    {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
