<?php

namespace Technodelight\Jira\Domain\Issue\Changelog;

use JsonException;

class Item
{
    private function __construct(
        private readonly ?string $from,
        private readonly ?string $to,
        private readonly ?string $fromString,
        private readonly ?string $toString,
        private readonly ?string $field,
        private readonly ?string $fieldId
    ) {}

    public static function fromArray(array $item): Item
    {
        return new self(
            $item['from'],
            $item['to'],
            $item['fromString'],
            $item['toString'],
            $item['field'],
            $item['fieldId'] ?? ''
        );
    }

    public function from(): string
    {
        return $this->from;
    }

    public function to(): string
    {
        return $this->to;
    }

    public function fromString(): string
    {
        return $this->normalise($this->fromString);
    }

    public function toString(): string
    {
        return $this->normalise($this->toString);
    }

    public function isMultiLine(): bool
    {
        return count(explode(PHP_EOL, $this->fromString())) > 1
            || count(explode(PHP_EOL, $this->toString())) > 1;
    }

    public function field(): string
    {
        return $this->field;
    }

    public function fieldId(): string
    {
        return $this->fieldId;
    }

    /** Strings should be normalised as if wysiwyg was used on windows, the contents would be json encoded */
    private function normalise(?string $string): string
    {
        try {
            // it can be json string as if wysiwyg was used on windows, the contents would be json encoded to preserve line endings
            if (null !== $string && $this->mightBeJson($string)) {
                $string = implode(PHP_EOL, json_decode($string, true, 512, JSON_THROW_ON_ERROR));
            }
        } catch (JsonException $e) {
            $string = null;
        }

        // line endings
        return strtr(
            $string ?? '',
            ["\r\n" => PHP_EOL]
        );
    }

    private function mightBeJson(string $string): bool
    {
        return str_starts_with($string, '{')
            || str_starts_with($string, '[');
    }
}
