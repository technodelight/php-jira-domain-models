<?php

namespace Technodelight\Jira\Domain;

use Countable;
use DateTime;
use Exception;
use Iterator;
use Technodelight\Jira\Domain\Worklog;
use Technodelight\Jira\Domain\WorklogCollection;

class DashboardCollection implements Iterator, Countable
{
    private const DATE_FORMAT = 'Y-m-d';

    private string $startDate;
    private string $endDate;
    private string $currentDate;
    private int $days;

    private function __construct(
        private readonly WorklogCollection $collection,
        private readonly DateTime $from,
        private readonly DateTime $to,
        private readonly array $workDays
    ) {
        $this->startDate = $this->findDate($collection, true)->format(self::DATE_FORMAT);
        $this->endDate = $this->findDate($collection, false)->format(self::DATE_FORMAT);
        $this->currentDate = $this->startDate;
    }

    public static function fromWorklogCollection(
        WorklogCollection $collection,
        DateTime $from,
        DateTime $to,
        array $workDays = [1, 2, 3, 4, 5]
    ): DashboardCollection {
        return new self($collection, $from, $to, $workDays);
    }

    public function start(): DateTime
    {
        return new DateTime($this->startDate);
    }

    public function end(): DateTime
    {
        return new DateTime($this->endDate);
    }

    public function from(): DateTime
    {
        return $this->from;
    }

    public function to(): DateTime
    {
        return $this->to;
    }

    public function daysRange(): int
    {
        return (int)$this->to()->diff($this->from())->format('%a') + 1;
    }

    public function days(): int
    {
        if (!isset($this->days)) {
            $this->days = 0;
            $date = clone $this->from();
            while ($date->format(self::DATE_FORMAT) <= $this->to()->format(self::DATE_FORMAT)) {
                if (in_array($date->format('N'), $this->workDays, true)) {
                    $this->days++;
                }
                $date->modify('+1 day');
            }
        }
        return $this->days;
    }

    /**
     * @param bool $onlyWorkDays return workdays only
     * @return DateTime[]
     */
    public function fromToDateRange(bool $onlyWorkDays = false): array
    {
        $dates = [];
        $current = clone $this->from;
        while ($current <= $this->to) {
            if ((in_array($current->format('N'), $this->workDays) && $onlyWorkDays) || $onlyWorkDays === false) {
                $dates[] = clone $current;
            }
            $current->modify('+1 day');
        }
        return $dates;
    }

    public function isADay(): bool
    {
        return $this->days() === 1;
    }

    public function isAWeek(): bool
    {
        return $this->daysRange() >= 5 && $this->daysRange() <= 7;
    }

    public function isAMonth(): bool
    {
        return $this->daysRange() >= 28 && $this->daysRange() <= 31;
    }

    /**
     * @return DashboardCollection[]
     * @throws Exception
     */
    public function splitToWeeks(): array
    {
        $weeks = [];
        foreach ($this as $date => $worklogCollection) {
            $week = $date->format('W');
            if (!isset($weeks[$week])) {
                $weeks[$week] = new self(
                    WorklogCollection::createEmpty(),
                    new DateTime(sprintf('%sW%s last monday', $date->format('Y'), $week)),
                    new DateTime(sprintf('%sW%s sunday', $date->format('Y'), $week)),
                    $this->workDays
                );
            }
            /** @var DashboardCollection $currentWeek */
            $currentWeek = $weeks[$week];
            $currentWeek->collection->merge($worklogCollection);
        }
        foreach ($weeks as $dashCollection) {
            $dashCollection->startDate = $this->findDate($dashCollection->collection, true)->format(self::DATE_FORMAT);
            $dashCollection->endDate = $this->findDate($dashCollection->collection, false)->format(self::DATE_FORMAT);
            $dashCollection->currentDate = $dashCollection->startDate;
        }

        return $weeks;
    }

    public function findMatchingLogsForDate(DateTime $findDate): WorklogCollection
    {
        $matchingLogs = WorklogCollection::createEmpty();
        foreach ($this->collection as $worklog) {
            /** @var $worklog Worklog */
            if ($worklog->date()->format(self::DATE_FORMAT) == $findDate->format(self::DATE_FORMAT)) {
                $matchingLogs->push($worklog);
            }
        }
        return $matchingLogs;
    }

    public function current(): WorklogCollection
    {
        $matchingLogs = WorklogCollection::createEmpty();
        foreach ($this->collection as $worklog) {
            /** @var $worklog Worklog */
            if ($worklog->date()->format(self::DATE_FORMAT) == $this->currentDate) {
                $matchingLogs->push($worklog);
            }
        }
        return $matchingLogs;
    }

    public function next(): void
    {
        $this->currentDate = date(
            self::DATE_FORMAT,
            strtotime('+1 day', strtotime($this->currentDate))
        );
    }

    public function key(): ?DateTime
    {
        if ($this->currentDate <= $this->endDate) {
            return new DateTime($this->currentDate);
        }
        return null;
    }

    public function valid(): bool
    {
        return $this->currentDate <= $this->endDate;
    }

    public function rewind(): void
    {
        $this->currentDate = $this->startDate;
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function issuesCount(): int
    {
        return count($this->collection->issueKeys());
    }

    public function totalTimeSpentSeconds(): int
    {
        return $this->collection->totalTimeSpentSeconds();
    }

    private function findDate(WorklogCollection $collection, bool $findMinimum = true): DateTime
    {
        $current = false;
        foreach ($collection as $worklog) {
            /** @var $worklog Worklog */
            if (!$current) {
                $current = $worklog->date();
            } else {
                $current = $findMinimum ? min($current, $worklog->date()) : max($current, $worklog->date());
            }
        }
        return $current ?: new DateTime();
    }
}
