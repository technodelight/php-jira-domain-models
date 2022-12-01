<?php

namespace Technodelight\Jira\Domain\Project;

use DateTime;
use Technodelight\Jira\Domain\DateTimeFactory;

class Version
{
    private function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly bool $isReleased,
        private readonly string $releaseDate,
        private readonly string $description,
        private readonly bool $isArchived
    ) {}

    public static function fromArray(array $version): Version
    {
        return new self(
            (int)($version['id'] ?? 0),
            $version['name'],
            (bool)$version['released'],
            $version['releaseDate'] ?? null,
            $version['description'] ?? '',
            (bool)$version['archived']
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

    public function isReleased(): bool
    {
        return $this->isReleased;
    }

    public function releaseDate(): DateTime
    {
        return DateTimeFactory::fromString($this->releaseDate);
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }
}
