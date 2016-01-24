<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\StringConverter;

final class NumberMatcher extends Matcher
{
    const NUMBER_PATTERN = '/^@number@$/';

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!is_numeric($value)) {
            $this->error = sprintf("%s \"%s\" is not a valid number.", gettype($value), new StringConverter($value));
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match(self::NUMBER_PATTERN, $pattern);
    }
}
