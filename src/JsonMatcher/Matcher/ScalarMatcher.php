<?php

namespace JsonMatcher\Matcher;

class ScalarMatcher implements PropertyMatcher
{

    /**
     * {@inheritDoc}
     */
    public function match($matcher, $pattern)
    {
        return $matcher === $pattern;
    }

    public function canMatch($pattern)
    {
        return is_scalar($pattern);
    }

}