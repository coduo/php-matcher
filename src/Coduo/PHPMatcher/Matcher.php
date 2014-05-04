<?php
namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Matcher\PropertyMatcher;

class Matcher
{
    /**
     * @var Matcher\PropertyMatcher
     */
    private $matcher;

    /**
     * @param PropertyMatcher $matcher
     */
    public function __construct(PropertyMatcher $matcher)
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
