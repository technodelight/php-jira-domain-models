<?php

namespace Technodelight\Jira\Domain;

class User
{
    private function __construct(
        private readonly string $id,
        private readonly string $key,
        private readonly string $name,
        private readonly string $displayName,
        private readonly string $emailAddress,
        private readonly array $avatarUrls,
        private readonly bool $active,
        private readonly ?string $timeZone,
        private readonly ?string $locale,
    ) {}

    public static function fromArray(array $array): User
    {
        return new self(
            $array['accountId'],
            $array['key'] ?? '',
            $array['name'] ?? '',
            $array['displayName'] ?? '',
        $array['emailAddress'] ?? '',
        $array['avatarUrls'] ?? [],
            $array['active'] ?? true,
            $array['timeZone'] ?? '',
            $array['locale'] ?? null
        );
    }

    public function id(): string
    {
        return $this->id;
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

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    public function avatarUrls(): array
    {
        return $this->avatarUrls;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function timeZone(): ?string
    {
        return $this->timeZone;
    }

    public function locale(): ?string
    {
        return $this->locale;
    }

    public function __toString()
    {
        return $this->displayName();
    }
}
