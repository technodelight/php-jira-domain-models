<?php

namespace Technodelight\Jira\Domain;

class Transition
{
    private function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly int $resolvesToId,
        private readonly string $resolvesToName,
        private readonly string $resolvesToDescription,
    ) {}

    public static function fromArray(array $transition): Transition
    {
        return new self(
            (int) $transition['id'],
            $transition['name'],
            (int)$transition['to']['id'],
            $transition['to']['name'],
            $transition['to']['description']
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

    public function resolvesToId(): int
    {
        return $this->resolvesToId;
    }

    public function resolvesToName(): string
    {
        return $this->resolvesToName;
    }

    public function resolvesToDescription(): string
    {
        return $this->resolvesToDescription;
    }

    public function __toString()
    {
        return $this->name();
    }
}
