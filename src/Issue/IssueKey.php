<?php

namespace Technodelight\Jira\Domain\Issue;

use Technodelight\Jira\Domain\Exception\MissingIssueKeyException;
use Technodelight\Jira\Domain\Project\ProjectKey;

class IssueKey
{
    public const PATTERN = '[A-Z0-9]+-[0-9]+';

    private readonly string $projectKey;
    private readonly int $sequenceNumber;

    private function __construct(private readonly string $issueKey)
    {
        if (!preg_match('~^'.self::PATTERN.'$~', $issueKey)) {
            throw new MissingIssueKeyException;
        }

        [$projectKey, $sequenceNumber] = explode('-', $issueKey, 2);
        $this->projectKey = $projectKey;
        $this->sequenceNumber = (int)$sequenceNumber;
    }

    public static function fromString($issueKey): IssueKey
    {
        return new self($issueKey);
    }

    public function projectKey(): ProjectKey
    {
        return ProjectKey::fromString($this->projectKey);
    }

    public function issueKey(): string
    {
        return $this->issueKey;
    }

    public function sequenceNumber(): int
    {
        return $this->sequenceNumber;
    }

    public function __toString()
    {
        return (string) $this->issueKey;
    }
}
