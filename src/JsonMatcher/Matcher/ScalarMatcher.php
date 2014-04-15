<?php

namespace JsonMatcher\Matcher;

class ScalarMatcher implements PropertyMatcher
{
    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        return $value === $pattern;
    }

    public function canMatch($pattern)
    {
        return is_scalar($pattern);
    }
}
