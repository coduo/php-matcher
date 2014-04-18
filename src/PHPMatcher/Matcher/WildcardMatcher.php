<?php

namespace PHPMatcher\Matcher;

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
        return is_string($pattern) && 0 !== preg_match("/^@(\*|wildcard)@$/", $pattern);
    }

}
