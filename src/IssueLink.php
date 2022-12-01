<?php

namespace Technodelight\Jira\Domain;

use Technodelight\Jira\Domain\IssueLink\Type;

class IssueLink
{
    private function __construct(
        private readonly int $id,
        private readonly Type $type,
        private readonly ?array $inwardIssue,
        private readonly ?array $outwardIssue
    ) {}

    public static function fromArray(array $array): IssueLink
    {
        return new self(
            isset($array['id']) ? (int)$array['id'] : null,
            $array['type'] instanceof Type ? $array['type'] : Type::fromArray($array['type']),
            $array['inwardIssue'] ?? null,
            $array['outwardIssue'] ?? null
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function inwardIssue(): ?Issue
    {
        if (!$this->inwardIssue) {
            return null;
        }

        return Issue::fromArray($this->inwardIssue);
    }

    public function outwardIssue(): ?Issue
    {
        if (!$this->outwardIssue) {
            return null;
        }

        return Issue::fromArray($this->outwardIssue);
    }

    public function isInward(): bool
    {
        return !is_null($this->inwardIssue);
    }

    public function isOutward(): bool
    {
        return !is_null($this->outwardIssue);
    }

    public function __toString(): string
    {
        if ($this->isInward()) {
            return sprintf('%s %s', $this->type()->inward(), $this->inwardIssue()?->issueKey());
        }

        return sprintf('%s %s', $this->type()->outward(), $this->outwardIssue()?->issueKey());
    }
}
