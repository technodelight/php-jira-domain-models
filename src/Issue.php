<?php

namespace Technodelight\Jira\Domain;

use DateTime;
use Technodelight\Jira\Domain\Issue\IssueId;
use Technodelight\Jira\Domain\Issue\IssueKey;
use Technodelight\Jira\Domain\Issue\IssueType;

/** @method  */
class Issue
{
    private function __construct(
        private readonly string $id,
        private readonly string $link,
        private readonly string $key,
        private readonly array $fields,
        private ?WorklogCollection $worklogs = null,
        private ?Issue $parent = null,
        private ?IssueCollection $subtasks = null,
        private ?array $comments = null,
        private ?array $attachments = null,
        private ?array $links = null
    ) {
    }

    public static function fromArray(array $resultArray): Issue
    {
        return new self(
            $resultArray['id'] ?? '',
            $resultArray['self'] ?? '',
            $resultArray['key'] ?? '',
            $resultArray['fields'] ?? []
        );
    }

    public function id(): IssueId
    {
        return IssueId::fromNumeric($this->id);
    }

    public function key(): IssueKey
    {
        return IssueKey::fromString($this->key);
    }

    /** @deprecated 2.26 use issueKey instead */
    public function ticketNumber(): void
    {
        throw new \BadMethodCallException(sprintf('calling %s is deprecated ', __METHOD__));
    }

    public function issueKey(): IssueKey
    {
        return IssueKey::fromString($this->key);
    }

    public function project(): Project
    {
        return Project::fromArray($this->findField('project') ?: []);
    }

    public function sequenceNumber(): int
    {
        [, $sequenceNumber] = explode('-', $this->key, 2);

        return (int) $sequenceNumber;
    }

    public function summary(): ?string
    {
        return $this->findField('summary');
    }

    public function description(): ?string
    {
        return $this->findField('description');
    }

    public function created(): DateTime
    {
        return DateTimeFactory::fromString($this->findField('created'));
    }

    public function status(): Status
    {
        if ($field = $this->findField('status')) {
            return Status::fromArray($field);
        }

        return Status::createEmpty();
    }

    public function statusCategory(): ?string
    {
        if ($field = $this->findField('status')) {
            return $field['statusCategory'];
        }

        return null;
    }

    public function environment(): ?string
    {
        return $this->findField('environment');
    }

    public function reporter(): string
    {
        if (($field = $this->findField('reporter'))) {
            return $field['displayName'] ?: '<unknown>';
        }
        return '';
    }

    public function creator(): string
    {
        if ($field = $this->findField('creator')) {
            return $field['displayName'] ?: '<unknown>';
        }
        return '';
    }

    public function creatorUser(): ?User
    {
        if (is_array($field = $this->findField('creator'))) {
            return User::fromArray($field);
        }

        return null;
    }

    public function assignee(): string
    {
        if ($field = $this->findField('assignee')) {
            return $field['displayName'] ?: '?';
        }
        return 'Unassigned';
    }

    public function assigneeUser(): ?User
    {
        if (is_array($field = $this->findField('assignee'))) {
            return User::fromArray($field);
        }

        return null;
    }

    public function progress(): ?array
    {
        return $this->findField('progress');
    }

    public function estimate(): int
    {
        return (int)$this->findField('timeestimate');
    }

    public function timeSpent(): int
    {
        return (int)$this->findField('timespent');
    }

    public function remainingEstimate(): ?int
    {
        if ($field = $this->findField('timetracking')) {
            return isset($field['remainingEstimateSeconds'])
                ? (int)$field['remainingEstimateSeconds']
                : null;
        }
        return null;
    }

    public function issueType(): IssueType
    {
        if ($field = $this->findField('issuetype')) {
            return IssueType::fromArray($field);
        }

        return IssueType::createEmpty();
    }

    public function priority(): Priority
    {
        if ($field = $this->findField('priority')) {
            return Priority::fromArray($field);
        }

        return Priority::createEmpty();
    }

    public function url(): string
    {
        $uriParts = parse_url($this->link);
        return sprintf(
            '%s://%s/browse/%s',
            $uriParts['scheme'],
            $uriParts['host'],
            $this->key
        );
    }

    public function components(): array
    {
        if ($comps = $this->findField('components')) {
            return array_map(static fn(array $field) => $field['name'], $comps);
        }

        return [];
    }

    public function worklogs(): WorklogCollection
    {
        if ($this->worklogs) {
            return $this->worklogs;
        }

        if ($field = $this->findField('worklog')) {
            return $this->worklogs = WorklogCollection::fromIssueArray($this, $field['worklogs']);
        }

        return $this->worklogs = WorklogCollection::createEmpty();
    }

    public function assignWorklogs(WorklogCollection $worklogs): void
    {
        $this->worklogs = $worklogs;
    }

    public function attachments(): array
    {
        if (!isset($this->attachments) && $attachments = $this->findField('attachment')) {
            $this->attachments = array_map(fn(array $attachment) => Attachment::fromArray($attachment, $this), $attachments);
        }

        return $this->attachments ?? [];
    }

    public function comments(): array
    {
        if (!empty($comments = $this->fields['comment']['comments']) && !isset($this->comments)) {
            $this->comments = array_map(static fn(array $comment) => Comment::fromArray($comment), $comments);
        }

        return $this->comments ?? [];
    }

    public function links(): array
    {
        if (!isset($this->links) && ($links = $this->findField('issuelinks'))) {
            $this->links = array_map(static fn(array $link) => IssueLink::fromArray($link), $links);
        }

        return $this->links ?? [];
    }

    public function parent(): ?Issue
    {
        if (!isset($this->parent) && $parent = $this->findField('parent')) {
            $this->parent = self::fromArray($parent);
        }

        return $this->parent;
    }

    public function subtasks(): IssueCollection
    {
        if (!isset($this->subtasks)) {
            $this->subtasks = IssueCollection::createEmpty();

            if ($subtasks = $this->findField('subtasks')) {
                foreach ($subtasks as $subtask) {
                    $this->subtasks->add(static::fromArray($subtask));
                }
            }
        }

        return $this->subtasks;
    }

    public function findField(string $fieldName): array|string|null
    {
        return $this->fields[$fieldName] ?? null;
    }
}
