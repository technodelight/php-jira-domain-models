<?php

namespace Technodelight\Jira\Domain\IssueLink;

class Type
{
    private function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $inward,
        private readonly string $outward
    ) {}

    public static function fromArray(array $array): Type
    {
        return new self(
            (int)$array['id'],
            $array['name'],
            $array['inward'],
            $array['outward']
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function inward(): string
    {
        return $this->inward;
    }

    public function outward(): string
    {
        return $this->outward;
    }
}
