<?php

namespace Technodelight\Jira\Domain;

use DateTime;
use RuntimeException;

class DateTimeFactory
{
    private const SUPPORTED_FORMATS = [DateFormat::DEFAULT_FORMAT, DateFormat::MICROTIME_FORMAT];

    public static function fromString(string $dateString): DateTime
    {
        foreach (self::SUPPORTED_FORMATS as $format) {
            $dateTime = DateTime::createFromFormat($format, $dateString);
            if ($dateTime !== false) {
                return $dateTime;
            }
        }

        throw new RuntimeException(
            sprintf('Cannot parse date string "%s" with any of supported formats', $dateString)
        );
    }
}