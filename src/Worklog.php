<?php

namespace Technodelight\Jira\Domain;

use DateTime;
use Technodelight\Jira\Domain\Issue\IssueKey;
use Technodelight\Jira\Domain\Issue\IssueId;
use Technodelight\Jira\Domain\Worklog\WorklogId;
use UnexpectedValueException;

class Worklog
{
    private ?int $issueId = null;
    private ?string $issueKey = null;
    private ?Issue $issue = null;
    private DateTime $date;

    private function __construct(
        int|string|IssueId|IssueKey $issueKeyOrId,
        private readonly string $worklogId,
        private ?string $comment,
        string|DateTime $date,
        private int $timeSpentSeconds,
        private readonly ?array $author = null
    ) {
        if (is_numeric($issueKeyOrId)) {
            $this->issueId = (int)$issueKeyOrId;
        } else if ($issueKeyOrId instanceof IssueId) {
            $this->issueId = $issueKeyOrId->id();
        }
        if ($issueKeyOrId instanceof IssueKey) {
            $this->issueKey = $issueKeyOrId->issueKey();
        } else if (is_string($issueKeyOrId) && !isset($this->issueId)) {
            $this->issueKey = $issueKeyOrId;
        }
        if (is_string($date)) {
            $this->date = DateTimeFactory::fromString($date);
        }
    }

    public static function fromArray(array $record, IssueId|IssueKey $issueKey): Worklog
    {
        return new self(
            $issueKey,
            $record['id'],
            $record['comment'] ?? null,
            $record['started'],
            $record['timeSpentSeconds'],
            $record['author'] ?? null
        );
    }

    public static function fromIssueAndArray(Issue $issue, array $record): Worklog
    {
        $worklog = self::fromArray($record, $issue->key());
        $worklog->issue = $issue;
        return $worklog;
    }

    public function issueKey(): ?IssueKey
    {
        return isset($this->issueKey) ? IssueKey::fromString($this->issueKey) : null;
    }

    public function issueId(): ?IssueId
    {
        return isset($this->issueId) ? IssueId::fromNumeric($this->issueId) : null;
    }

    /** Can be one of: issueKey or issueId */
    public function issueIdentifier(): IssueKey|IssueId
    {
        return $this->issueKey ?? $this->issueId;
    }

    public function issue(): ?Issue
    {
        return $this->issue;
    }

    public function assignIssue(Issue $issue): void
    {
        if (!empty($this->issueKey) && ((string)$issue->issueKey() !== $this->issueKey)) {
            throw new UnexpectedValueException(
                'Unable to assign issue as issue key is not matching with work log'
            );
        }
        $this->issue = $issue;
        $issue->worklogs()->push($this);
    }

    public function id(): WorklogId
    {
        return WorklogId::fromNumeric($this->worklogId);
    }

    public function author(): ?User
    {
        if ($this->author) {
            return User::fromArray($this->author);
        }
        return null;
    }

    public function comment(string $comment = null): self|string
    {
        if ($comment) {
            $this->comment = $comment;
            return $this;
        }

        return $this->comment;
    }

    public function date(DateTime|string $date = null): self|Datetime
    {
        if ($date) {
            $this->date = $date instanceof DateTime ? $date : DateTimeFactory::fromString($date);
        }

        return $this->date;
    }

    public function timeSpentSeconds(int $seconds = null): self|int
    {
        if (!is_null($seconds)) {
            $this->timeSpentSeconds = $seconds;
        }

        return $this->timeSpentSeconds;
    }

    public function isSame(Worklog $log): bool
    {
        return [$log->timeSpentSeconds, $log->comment, $log->date, $log->author()?->id()]
            === [$this->timeSpentSeconds, $this->comment, $this->date, $this->author()?->id()];
    }
}
