<?php

namespace PHPMatcher\Matcher;

class TypeMatcher implements PropertyMatcher
{
    const MATCH_PATTERN = "/^@(string|integer|boolean|double|array)@$/";

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        return gettype($value) === $this->extractType($pattern);
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
