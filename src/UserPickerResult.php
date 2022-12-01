<?php

namespace Technodelight\Jira\Domain;

class UserPickerResult
{
    private function __construct(
        private readonly string $key,
        private readonly string $name,
        private readonly string $displayName,
        private readonly string $html
    ) {}

    public static function fromArray(array $result): UserPickerResult
    {
        return new self(
            $result['key'],
            $result['name'],
            $result['displayName'],
            $result['html'],
        );
    }

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }

    public function html(): string
    {
        return $this->html;
    }
}
