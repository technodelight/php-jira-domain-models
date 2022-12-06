<?php

namespace Technodelight\Jira\Domain;

use Iterator;
use Countable;
use RangeException;
use Technodelight\Jira\Domain\Issue\IssueId;
use Technodelight\Jira\Domain\Issue\IssueKey;

class IssueCollection implements Iterator, Countable
{
    private array $issues = [];

    public function __construct(private readonly int $startAt, private int $total, array $issues)
    {
        foreach ($issues as $issue) {
            $this->issues[] = Issue::fromArray($issue);
        }
    }

    public static function createEmpty(): IssueCollection
    {
        return new self(0, 0, []);
    }

    public static function fromSearchArray(array $resultArray): IssueCollection
    {
        return new self(
            $resultArray['startAt'],
            $resultArray['total'],
            $resultArray['issues']
        );
    }

    public function count(): int
    {
        return (int)count($this->issues);
    }

    public function total(): int
    {
        return $this->total;
    }

    public function startAt(): int
    {
        return $this->startAt;
    }

    public function isLast(): bool
    {
        return ($this->startAt + 50) >= $this->total;
    }

    public function current(): Issue|false
    {
        return current($this->issues);
    }

    public function next(): void
    {
        next($this->issues);
    }

    public function key(): ?int
    {
        return key($this->issues);
    }

    public function rewind(): void
    {
        reset($this->issues);
    }

    public function valid(): bool
    {
        $item = current($this->issues);
        return $item !== false;
    }

    public function keys(): array
    {
        $keys = [];
        foreach ($this as $issue) {
            $keys[] = $issue->issueKey();
        }
        return $keys;
    }

    public function merge(IssueCollection $collection): void
    {
        foreach ($collection as $issue) {
            $this->add($issue);
        }
    }

    public function limit(int $limit): void
    {
        $this->issues = array_splice($this->issues, 0, $limit);
    }

    public function add(Issue $issue): void
    {
        if (!$this->findById($issue->id())) {
            $this->issues[] = $issue;
            ++$this->total;
        }
    }

    public function has($issueKey): bool
    {
        return $this->find($issueKey) instanceof Issue;
    }

    public function sort(callable $callable): void
    {
        uasort($this->issues, $callable);
    }

    public function find(string|IssueKey $issueKey): Issue
    {
        if (is_string($issueKey)) {
            $issueKey = IssueKey::fromString($issueKey);
        }

        foreach ($this as $issue) {
            if ($issue->issueKey()->isSame($issueKey)) {
                return $issue;
            }
        }

        throw new RangeException(
            sprintf('Cannot find issue by key: %s', $issueKey)
        );
    }

    public function findById(int|IssueId $id): Issue|null
    {
        if (is_int($id)) {
            $id = IssueId::fromNumeric($id);
        }

        foreach ($this as $issue) {
            if ($issue->id()->isSame($id)) {
                return $issue;
            }
        }

        return null;
    }

    public function findByIndex(int $index): Issue|null
    {
        foreach ($this as $idx => $issue) {
            if ($idx === $index) {
                return $issue;
            }
        }

        return null;
    }
}
