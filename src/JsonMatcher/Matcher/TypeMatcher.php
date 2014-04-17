<?php

namespace JsonMatcher\Matcher;

class TypeMatcher implements PropertyMatcher
{
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
        return is_string($pattern) && 0 !== preg_match("/^@(string|integer|boolean|double|array)@$/", $pattern);
    }

    private function extractType($pattern)
    {
        return str_replace("@", "", $pattern);
    }
}
