<?php

namespace Technodelight\Jira\Domain\Issue;

use DateTime;
use Technodelight\Jira\Domain\DateFormat;
use Technodelight\Jira\Domain\DateTimeFactory;
use Technodelight\Jira\Domain\Issue\Changelog\Item;
use Technodelight\Jira\Domain\NumericId;
use Technodelight\Jira\Domain\User;

class Changelog
{
    private function __construct(
        private readonly int $id,
        private readonly string $issueKey,
        private readonly string $created,
        private readonly ?array $author,
        private readonly array $items
    ) {}

    public static function fromArray(array $changeLog, $issueKey): Changelog
    {
        return new self(
            (int)$changeLog['id'],
            (string)$issueKey,
            $changeLog['created'],
            $changeLog['author'] ?? null,
            $changeLog['items'] ?? []
        );
    }

    public function id(): NumericId
    {
        return NumericId::fromNumeric($this->id);
    }

    public function issueKey(): IssueKey
    {
        return IssueKey::fromString($this->issueKey);
    }

    public function created(): DateTime
    {
        return DateTimeFactory::fromString($this->created);
    }

    public function author(): ?User
    {
        if ($this->author) {
            return User::fromArray($this->author);
        }

        return null;
    }

    /** @return Item[] */
    public function items(): array
    {
        return array_map(static fn (array $item) => Item::fromArray($item), $this->items);
    }
}
