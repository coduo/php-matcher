<?php

namespace JsonMatcher\Matcher;

interface PropertyMatcher
{
    /**
     * Matches value against the pattern
     *
     * @param $value
     * @param $pattern
     * @return boolean
     */
    public function match($value, $pattern);

    /**
     * Checks if matcher can match the pattern
     *
     * @param $pattern
     * @return boolean
     */
    public function canMatch($pattern);

}
