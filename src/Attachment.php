<?php

namespace Technodelight\Jira\Domain;

use DateTime;

class Attachment
{
    private function __construct(
        private readonly Issue $issue,
        private readonly string $id,
        private readonly ?array $author,
        private readonly string $created,
        private readonly int $size,
        private readonly string $filename,
        private readonly string $url
    ) {}

    public static function fromArray(array $attachment, Issue $issue)
    {
        return new Attachment(
            $issue,
            (int)($attachment['id'] ?? ''),
            $attachment['author'] ?? [],
            $attachment['created'] ?? '',
            (int)($attachment['size'] ?? 0),
            $attachment['filename'] ?? '',
            $attachment['content'] ?? ''
        );
    }

    public function issue(): Issue
    {
        return $this->issue;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function author(): ?User
    {
        if ($this->author) {
            return User::fromArray($this->author);
        }

        return null;
    }

    public function created(): DateTime
    {
        return DateTimeFactory::fromString($this->created);
    }

    public function size(): int
    {
        return $this->size;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function url(): string
    {
        return $this->url;
    }
}
