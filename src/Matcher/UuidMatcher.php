<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\StringConverter;

final class UuidMatcher extends Matcher
{
    const UUID_PATTERN = '/^@uuid@$/';
    const UUID_FORMAT_PATTERN = '|^[\da-f]{8}-[\da-f]{4}-4[\da-f]{3}-[89ab][\da-f]{3}-[\da-f]{12}$|';

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!is_string($value)) {
            $this->error = sprintf(
                "%s \"%s\" is not a valid UUID: not a string.",
                gettype($value),
                new StringConverter($value)
            );
            return false;
        }

        if (1 !== preg_match(self::UUID_FORMAT_PATTERN, $value)) {
            $this->error = sprintf(
                "%s \"%s\" is not a valid UUID: invalid format.",
                gettype($value),
                $value
            );
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match(self::UUID_PATTERN, $pattern);
    }
}
