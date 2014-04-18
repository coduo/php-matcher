<?php

namespace PHPMatcher\Matcher;

class ScalarMatcher implements PropertyMatcher
{
    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        return $value === $pattern;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_scalar($pattern);
    }
}
