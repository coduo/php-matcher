<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Matcher\ValueMatcher;

final class Matcher
{
    private $matcher;

    public function __construct(ValueMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    public function match($value, $pattern) : bool
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
