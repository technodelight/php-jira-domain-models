<?php

namespace Technodelight\Jira\Domain\Issue;

use InvalidArgumentException;
use Technodelight\Jira\Domain\Issue\Meta\Field;

class Meta
{
    private function __construct(
        private readonly string $issueKey,
        private readonly array $fields
    ) {}

    public static function fromArrayAndIssueKey(array $metaFields, string $issueKey): Meta
    {
        return new self($issueKey, $metaFields);
    }

    public function issueKey(): IssueKey
    {
        return IssueKey::fromString($this->issueKey);
    }

    /** @return Field[] */
    public function fields(): array
    {
        return array_map(static fn (array $meta) => Field::fromArray($meta), $this->fields);
    }

    /**
     * @param string $fieldName
     * @return Field
     * @throws InvalidArgumentException
     */
    public function field(string $fieldName)
    {
        foreach ($this->fields() as $field) {
            if ($field->key() === $fieldName || $field->name() === $fieldName) {
                return $field;
            }
        }

        throw new InvalidArgumentException(
            sprintf('No meta found for field "%s"', $fieldName)
        );
    }
}
