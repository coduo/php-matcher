<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

interface ValueMatcher
{
    /**
     * Matches value against the pattern
     */
    public function match($value, $pattern) : bool;

    /**
     * Checks if matcher can match the pattern
     */
    public function canMatch($pattern) : bool;

    /**
     * Returns a string description why matching failed
     *
     * @return null|string
     */
    public function getError();
}
