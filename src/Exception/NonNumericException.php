<?php

namespace Technodelight\Jira\Domain\Exception;

use UnexpectedValueException;

class NonNumericException extends UnexpectedValueException  implements ArgumentException
{
    public static function fromString(int|string $string): self
    {
        return new self(sprintf('"%s" is non numeric and cannot be used as ID!', $string));
    }
}