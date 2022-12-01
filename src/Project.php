<?php

namespace Technodelight\Jira\Domain;

use Technodelight\Jira\Domain\Project\ProjectId;
use Technodelight\Jira\Domain\Project\Version;

class Project
{
    private function __construct(
        private readonly string|int $id,
        private readonly string $key,
        private readonly string $name,
        private readonly string $description,
        private readonly ?string $projectTypeKey,
        private readonly array $versions,
        private readonly ?array $lead,
        private readonly array $components,
        private readonly array $issueTypes
    ) {}

    public static function fromArray(array $project): Project
    {
        return new self(
            $project['id'],
            $project['key'],
            $project['name'],
            $project['description'] ?? '',
            $project['projectTypeKey'] ?? null,
            $project['versions'] ?? [],
            $project['lead'] ?? null,
            $project['components'] ?? [],
            $project['issueTypes'] ?? []
        );
    }

    public function id(): ProjectId
    {
        return ProjectId::fromNumeric($this->id);
    }

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function projectTypeKey(): string
    {
        return $this->projectTypeKey;
    }

    public function description(): string
    {
        return $this->description;
    }

    /** @return Version[] */
    public function versions(): array
    {
        return array_map(static fn (array $version) => Version::fromArray($version), $this->versions);
    }

    public function lead(): ?User
    {
        if ($this->lead) {
            return User::fromArray($this->lead);
        }
        return null;
    }

    public function components(): array
    {
        return $this->components;
    }

    public function issueTypes(): array
    {
        return $this->issueTypes;
    }
}
