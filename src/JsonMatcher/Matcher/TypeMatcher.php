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

    public function canMatch($pattern)
    {
        return is_scalar($pattern);
    }

    private function extractType($pattern)
    {
        return str_replace("@", "", $pattern);
    }

}
