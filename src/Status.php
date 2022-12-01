<?php

namespace Technodelight\Jira\Domain;

class Status
{
    private function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $description,
        private readonly string $statusCategory,
        private readonly string $statusCategoryColor
    ) {}

    public static function fromArray(array $status): Status
    {
        return new self(
            (int)$status['id'],
            $status['name'],
            $status['description'],
            $status['statusCategory']['name'] ?? '',
            $status['statusCategory']['colorName'] ?? ''
        );
    }

    public static function createEmpty(): Status
    {
        return new self(
            0,
            '',
            '',
            '',
            ''
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function statusCategory(): string
    {
        return $this->statusCategory;
    }

    public function statusCategoryColor(): string
    {
        return $this->statusCategoryColor;
    }

    public function __toString()
    {
        return $this->name();
    }
}
