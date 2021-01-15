<?php
declare(strict_types=1);

namespace App\Value;

class XmlParts
{
    private string $ruleCode;
    private string $description;
    /**
     * @var Diff[]
     */
    private array $diffs;
    /**
     * @var Url[]
     */
    private array $links;

    /**
     * @param Diff[] $diffs
     * @param Url[] $links
     */
    public function __construct(string $ruleCode, string $description, array $diffs, array $links)
    {
        $this->ruleCode = $ruleCode;
        $this->description = $description;
        $this->diffs = $diffs;
        $this->links = $links;
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

    /**
     * @return Url[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
