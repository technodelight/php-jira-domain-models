<?php

namespace Technodelight\Jira\Domain\Issue;

class IssueType
{
    private function __construct(
        private readonly string $name,
        private readonly string $description
    ) {}

    public static function fromArray(array $issueType): IssueType
    {
        return new self(
            $issueType['name'] ?? '',
            $issueType['description'] ?? ''
        );
    }

    public static function createEmpty(): IssueType
    {
        return self::fromArray([]);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function __toString()
    {
        return $this->name;
    }
}
