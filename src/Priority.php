<?php

namespace Technodelight\Jira\Domain;

use Technodelight\Jira\Domain\Priority\PriorityId;

class Priority
{
    private function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $description,
        private readonly string $statusColor
    ) {}

    public static function fromArray(array $status): Priority
    {
        return new self(
            $status['id'],
            $status['name'],
            $status['description'] ?? '',
            $status['statusColor'] ?? ''
        );
    }

    public function id(): PriorityId
    {
        return PriorityId::fromNumeric($this->id);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function statusColor(): string
    {
        return $this->statusColor;
    }

    public function __toString()
    {
        return $this->name();
    }
}
