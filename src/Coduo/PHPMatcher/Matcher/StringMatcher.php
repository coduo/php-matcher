<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\String;

class StringMatcher extends Matcher
{
    const STRING_PATTERN = '/^@string@$/';

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!is_string($value)) {
            $this->error = sprintf("%s \"%s\" is not a valid string.", gettype($value), new String($value));
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match(self::STRING_PATTERN, $pattern);
    }
}
