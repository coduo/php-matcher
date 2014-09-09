<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\String;

class DoubleMatcher extends Matcher
{
    const DOUBLE_PATTERN = '/^@double@$/';

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!is_double($value)) {
            $this->error = sprintf("%s \"%s\" is not a valid double.", gettype($value), new String($value));
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match(self::DOUBLE_PATTERN, $pattern);
    }
}
