<?php

namespace Technodelight\Jira\Domain\Issue;

use Technodelight\Jira\Domain\Exception\MissingIssueKeyException;
use Technodelight\Jira\Domain\Project\ProjectKey;

class IssueKey
{
    const REGEX = '~^[A-Z0-9]+-[0-9]+$~';
    const PATTERN = '[A-Z0-9]+-[0-9]+';

    private $issueKey;
    private $issueId;
    private $projectKey;

    private function __construct($issueKey)
    {
        if (!preg_match(self::REGEX, $issueKey)) {
            throw new MissingIssueKeyException;
        }
        $this->issueKey = $issueKey;
        list ($projectKey, $issueId) = explode('-', $issueKey, 2);
        $this->projectKey = $projectKey;
        $this->issueId = $issueId;
    }

    public static function fromString($issueKey)
    {
        return new IssueKey($issueKey);
    }

    public function projectKey()
    {
        return ProjectKey::fromString($this->projectKey);
    }

    public function issueId()
    {
        return $this->issueId;
    }

    public function __toString()
    {
        return (string) $this->issueKey;
    }
}
