<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\String;

class IntegerMatcher extends Matcher
{
    const INTEGER_PATTERN = '/^@integer@$/';

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!is_integer($value)) {
            $this->error = sprintf("%s \"%s\" is not a valid integer.", gettype($value), new String($value));
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match(self::INTEGER_PATTERN, $pattern);
    }
}
