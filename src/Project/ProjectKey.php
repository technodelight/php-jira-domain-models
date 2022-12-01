<?php

namespace Technodelight\Jira\Domain\Project;

use Technodelight\Jira\Domain\Exception\MissingProjectKeyException;

class ProjectKey
{
    private function __construct(private readonly string $projectKey) {}

    public static function fromString($string): ProjectKey
    {
        if (empty(trim($string))) {
            throw new MissingProjectKeyException();
        }
        return new self(strtoupper(trim($string)));
    }

    public function __toString()
    {
        return $this->projectKey;
    }
}
