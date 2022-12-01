<?php

namespace Technodelight\Jira\Domain\Exception;

use UnexpectedValueException;

class MissingIssueKeyException extends UnexpectedValueException implements ArgumentException
{
    protected $message = 'The "IssueKey" parameter is missing';
}
