<?php

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Matcher\ValueMatcher;

final class Matcher
{
    /**
     * @var ValueMatcher
     */
    private $matcher;

    /**
     * @param ValueMatcher $matcher
     */
    public function __construct(ValueMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * @param mixed $value
     * @param mixed $pattern
     * @return bool
     */
    public function match($value, $pattern)
    {
        return $this->matcher->match($value, $pattern);
    }

    /**
     * @return null|string
     */
    public function getError()
    {
        return $this->matcher->getError();
    }
}
