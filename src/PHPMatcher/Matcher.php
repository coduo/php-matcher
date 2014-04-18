<?php
namespace PHPMatcher;

use PHPMatcher\Matcher\PropertyMatcher;

class Matcher implements PropertyMatcher
{
    /**
     * @var Matcher\PropertyMatcher
     */
    private $matcher;

    public function __construct(PropertyMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    public function match($value, $pattern)
    {
        return $this->matcher->match($value, $pattern);
    }

    public function canMatch($pattern)
    {
        return true;
    }
}
