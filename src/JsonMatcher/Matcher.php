<?php
namespace JsonMatcher;

use JsonMatcher\Matcher\PropertyMatcher;

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

    public function match($matcher, $pattern)
    {
        return $this->matcher->match($matcher, $pattern);
    }

    public function canMatch($pattern)
    {
        return true;
    }


}