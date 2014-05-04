<?php
namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Matcher\PropertyMatcher;

class Matcher
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
}
