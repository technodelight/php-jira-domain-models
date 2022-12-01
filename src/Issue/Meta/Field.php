<?php

namespace Technodelight\Jira\Domain\Issue\Meta;

class Field
{
    private function __construct(
        private readonly string $key,
        private readonly string $name,
        private readonly array $operations,
        private readonly array $schema,
        private readonly bool $required,
        private readonly bool $custom,
        private readonly array $allowedValues,
        private readonly string $autocompleteUrl
    ) {}

    public static function fromArray(array $meta)
    {
        return new self(
            $meta['key'],
            $meta['name'],
            $meta['operations'],
            $meta['schema'] ?? [],
            (bool)$meta['required'],
            (bool)($meta['schema']['custom'] ?? false),
            $meta['allowedValues'] ?? [],
            $meta['autoCompleteUrl'] ?? ''
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

    public function operations(): array
    {
        return $this->operations;
    }

    public function schema(): array
    {
        return $this->schema;
    }

    public function schemaType(): string
    {
        return $this->schema['type'] ?? '';
    }

    public function schemaItemType(): string
    {
        return $this->schema['items'] ?? '';
    }

    public function isRequired(): bool
    {
        return $this->required === true;
    }

    public function isCustom(): bool
    {
        return $this->custom;
    }

    public function allowedValues(): array
    {
        return array_map(
            static function (array|string $valueArray) {
                if (is_array($valueArray)) {
                    if (isset($valueArray['name'])) {
                        return $valueArray['name'];
                    }

                    if (isset($valueArray['label'])) {
                        return $valueArray['label'];
                    }
                }

                return $valueArray;
            },
            $this->allowedValues
        );
    }

    public function autocompleteUrl(): string
    {
        return $this->autocompleteUrl;
    }

    public function __toString()
    {
        return $this->key();
    }
}
