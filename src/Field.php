<?php

namespace Technodelight\Jira\Domain;

class Field
{
    private string $id;
    private string $key;
    private string $name;
    private bool $custom;
    private array $clauseNames;
    private array $schema;

    public static function fromArray(array $field): Field
    {
        $instance = new self;

        $instance->id = $field['id'];
        $instance->key = $field['key'];
        $instance->name = $field['name'];
        $instance->custom = $field['custom'];
        $instance->clauseNames = $field['clauseNames'] ?? [];
        $instance->schema = $field['schema'] ?? [];

        return $instance;
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

    public function isCustom(): bool
    {
        return $this->custom;
    }

    /** @return string[] */
    public function clauseNames(): array
    {
        return $this->clauseNames;
    }

    public function schema(): array
    {
        return $this->schema;
    }

    public function schemaType(): string
    {
        return $this->schema['type'] ?? '';
    }

    public function schemaItemType()
    {
        return $this->schema['items'] ?? '';
    }

    private function __construct()
    {
    }

    public function __toString()
    {
        return $this->key();
    }
}
