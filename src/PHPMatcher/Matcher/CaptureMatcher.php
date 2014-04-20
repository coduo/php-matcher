<?php

namespace PHPMatcher\Matcher;

class CaptureMatcher implements PropertyMatcher, \ArrayAccess
{
    const MATCH_PATTERN = "/^:.*:$/";

    private $captures = array();

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        $this->captures[$this->extractPattern($pattern)] = $value;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match(self::MATCH_PATTERN, $pattern);
    }

    private function extractPattern($pattern)
    {
        return str_replace(":", "", $pattern);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->captures[] = $value;
        } else {
            $this->captures[$offset] = $value;
        }
    }
    public function offsetExists($offset)
    {
        return isset($this->captures[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->captures[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->captures[$offset]) ? $this->captures[$offset] : null;
    }
}
