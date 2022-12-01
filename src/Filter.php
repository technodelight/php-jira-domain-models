<?php

namespace Technodelight\Jira\Domain;

use Technodelight\Jira\Domain\Filter\FilterId;

final class Filter
{
    private function __construct(private readonly array $filter) {}

    public static function fromArray(array $filter): Filter
    {
        return new self($filter);
    }

    public function id(): FilterId
    {
        return FilterId::fromNumeric($this->filter['id']);
    }

    public function isFavourite(): bool
    {
        return !empty($this->filter['favourite']);
    }

    public function jql(): string
    {
        return $this->filter['jql'] ?? '';
    }

    public function name(): string
    {
        return $this->filter['name'] ?? '';
    }

    public function description(): string
    {
        return !empty($this->filter['description']) ? $this->filter['description'] : '';
    }

    public function owner(): User
    {
        return User::fromArray($this->filter['owner']);
    }

    public function favouritedCount(): int
    {
        return !empty($this->filter['favouritedCount']) ? (int) $this->filter['favouritedCount'] : 0;
    }
}
