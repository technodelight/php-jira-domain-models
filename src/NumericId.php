<?php

namespace Technodelight\Jira\Domain;

use Technodelight\Jira\Domain\Exception\NonNumericException;

class NumericId
{
    private function __construct(private readonly int $id)
    {}

    public static function fromNumeric(int|string $id): static
    {
        if (!is_numeric($id) && !empty($id)) {
            throw NonNumericException::fromString($id);
        }

        return new static((int)trim($id));
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