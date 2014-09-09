<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\String;

/**
 * @deprecated since 1.1, to be removed in 2.0. Use StringMatcher, NumberMatcher, IntegerMatcher,
 * BooleanMatcher, ArrayMatcher instead.
 */
class TypeMatcher extends Matcher
{
    const MATCH_PATTERN = "/^@(string|integer|boolean|double|array)@$/";

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (gettype($value) !== $this->extractType($pattern)) {
            $this->error = sprintf("%s \"%s\" does not match %s pattern.", gettype($value), new String($value), $pattern);
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match(self::MATCH_PATTERN, $pattern);
    }


    private function extractType($pattern)
    {
        return str_replace("@", "", $pattern);
    }
}
