<?php

namespace Technodelight\Jira\Domain;

use DateTime;
use Technodelight\Jira\Domain\Comment\CommentId;

class Comment
{
    private function __construct(
        private readonly string $id,
        private readonly array $author,
        private readonly string $body,
        private readonly string $created,
        private readonly string $updated,
        private readonly array $visibility
    ) {}

    public static function fromArray(array $record): Comment
    {
        return new self(
            $record['id'],
            $record['author'],
            $record['body'],
            $record['created'],
            $record['updated'],
            $record['visibility'] ?? []
        );
    }

    public function id(): CommentId
    {
        return CommentId::fromString($this->id);
    }

    public function body(): string
    {
        return $this->body;
    }

    public function author(): User
    {
        return User::fromArray($this->author);
    }

    public function visibility(): string
    {
        return $this->visibility['value'] ?? '';
    }

    public function created(): DateTime
    {
        return DateTimeFactory::fromString($this->created);
    }

    public function updated(): DateTime
    {
        return DateTimeFactory::fromString($this->updated);
    }

    public function __toString()
    {
        return $this->body;
    }
}
