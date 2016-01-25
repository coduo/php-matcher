<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class MatchRegex implements PatternExpander
{
    /**
     * @var null|string
     */
    private $error;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        if (!is_string($pattern)) {
            throw new \InvalidArgumentException("Regex pattern must be a string.");
        }

        if (!is_string($pattern) || @preg_match($pattern, '') === false) {
            throw new \InvalidArgumentException("Regex pattern must be a valid one.");
        }

        $this->pattern = $pattern;
    }

    /**
     * @param string $value
     *
     * @return boolean
     */
    public function match($value)
    {
        if (false === is_string($value)) {
            $this->error = sprintf("Match expander require \"string\", got \"%s\".", new StringConverter($value));

            return false;
        }

        if (1 !== preg_match($this->pattern, $value)) {
            $this->error = sprintf("string \"%s\" don't match pattern %s.", $value, $this->pattern);

            return false;
        }

        return true;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }
}
