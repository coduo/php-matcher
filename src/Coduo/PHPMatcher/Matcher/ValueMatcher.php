<?php

namespace Coduo\PHPMatcher\Matcher;

interface ValueMatcher
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

    /**
     * Returns a string description why matching failed
     *
     * @return null|string
     */
    public function getError();
}
