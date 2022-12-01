<?php

namespace Technodelight\Jira\Domain;

use CallbackFilterIterator;
use Countable;
use DateTime;
use Iterator;
use Technodelight\Jira\Domain\Issue\IssueKey;

class WorklogCollection implements Iterator, Countable
{
    private function __construct(
        private array $worklogs,
        private int $maxResults = 0,
        private int $total = 0
    ) {}

    public static function fromIssueArray(Issue $issue, array $worklogs): WorklogCollection
    {
        return new self(
            array_map(static fn(array $worklog) => Worklog::fromArray($worklog, $issue->issueKey()), $worklogs),
            count($worklogs),
            count($worklogs)
        );
    }

    public static function fromIterator(CallbackFilterIterator $iterator): WorklogCollection
    {
        $worklogs = iterator_to_array($iterator);
        return new self(
            $worklogs,
            count($worklogs),
            count($worklogs)
        );
    }

    public static function createEmpty(): WorklogCollection
    {
        return new self([]);
    }

    public function count(): int
    {
        return count($this->worklogs);
    }

    public function current(): Worklog|false
    {
        return current($this->worklogs);
    }

    public function next(): void
    {
        next($this->worklogs);
    }

    public function key(): int
    {
        return key($this->worklogs);
    }

    public function rewind(): void
    {
        reset($this->worklogs);
    }

    public function valid(): bool
    {
        $item = current($this->worklogs);
        return $item !== false;
    }

    public function push(Worklog $worklog): void
    {
        if (!in_array($worklog, $this->worklogs, true)) {
            $this->worklogs[] = $worklog;
            ++$this->maxResults;
            ++$this->total;
        }
    }

    public function merge(WorklogCollection $collection): void
    {
        $this->worklogs = array_merge($this->worklogs, $collection->worklogs);
        $this->maxResults = count($this->worklogs);
        $this->total = count($this->worklogs);
    }

    public function totalTimeSpentSeconds(): int
    {
        return (int)array_sum(array_map(static fn(Worklog $worklog) => $worklog->timeSpentSeconds(), $this->worklogs));
    }

    public function issueKeys(): array
    {
        return array_unique(array_map(static fn(Worklog $worklog) => $worklog->issueKey(), $this->worklogs));
    }

    public function issueCount(): int
    {
        return count($this->issueKeys());
    }

    public function issues(): IssueCollection
    {
        $issues = IssueCollection::createEmpty();
        foreach ($this->worklogs as $worklog) {
            $issues->add($worklog->issue());
        }
        return $issues;
    }

    public function orderByCreatedDateDesc(): self
    {
        uasort($this->worklogs, static function (Worklog $a, Worklog $b) {
            if ($a->date() == $b->date()) {
                return 0;
            }
            return $a->date() > $b->date() ? -1 : 1;
        });

        return $this;
    }

    public function filterByLimit($limit): WorklogCollection
    {
        $count = 0;
        return self::fromIterator(
            new CallbackFilterIterator($this, static function() use ($limit, $count) {
                $count++;
                return $count <= $limit;
            })
        );
    }

    public function filterByUser(User $user): WorklogCollection
    {
        return self::fromIterator(
            new CallbackFilterIterator($this, static fn(Worklog $log) => $log->author()?->id() === $user->id())
        );
    }

    public function filterByDate(DateTime $from, DateTime $to): WorklogCollection
    {
        return self::fromIterator(
            new CallbackFilterIterator($this, static function(Worklog $log) use ($from, $to) {
                return $log->date()->format('Y-m-d') >= $from->format('Y-m-d')
                    && $log->date()->format('Y-m-d') <= $to->format('Y-m-d');
            })
        );
    }

    public function filterByIssueKey(IssueKey $issueKey): WorklogCollection
    {
        return self::fromIterator(
            new CallbackFilterIterator($this, static fn(Worklog $worklog) => $issueKey === $worklog->issueKey())
        );
    }
}
