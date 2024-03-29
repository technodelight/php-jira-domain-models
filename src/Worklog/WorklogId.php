<?php

namespace Technodelight\Jira\Domain\Worklog;

use Technodelight\Jira\Domain\Exception\NonNumericException;

class WorklogId
{
    private function __construct(private readonly int $id) {}

    public static function fromNumeric(int|string $id): WorklogId
    {
        if (!is_numeric($id) && !empty($id)) {
            throw NonNumericException::fromString($id);
        }

        return new self((int)trim($id));
    }

    public function id(): int
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string)$this->id;
    }
}
