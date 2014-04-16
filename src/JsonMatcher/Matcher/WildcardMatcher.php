<?php

namespace JsonMatcher\Matcher;

class WildcardMatcher implements PropertyMatcher
{
    /**
     * {@inheritDoc}
     */
    public function match($matcher, $pattern)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return '*' === $pattern;
    }

}
