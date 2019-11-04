<?php

namespace Technodelight\Jira\Domain;

use DateTime;
use RuntimeException;

class DateTimeFactory
{
    private static $supportedFormats = [DateFormat::DEFAULT_FORMAT, DateFormat::MICROTIME_FORMAT];

    /**
     * @param string $dateString
     * @return DateTime
     * @throws RuntimeException
     */
    public static function fromString($dateString)
    {
        foreach (self::$supportedFormats as $format) {
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