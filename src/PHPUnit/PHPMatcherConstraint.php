<?php

namespace Coduo\PHPMatcher\PHPUnit;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;

final class PHPMatcherConstraint extends \PHPUnit_Framework_Constraint
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        parent::__construct();

        $this->pattern = $pattern;
        $this->matcher = $this->createMatcher();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'matches the pattern';
    }

    /**
     * @param mixed $other
     *
     * @return null|string
     */
    protected function additionalFailureDescription($other)
    {
        return $this->matcher->getError();
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function matches($value)
    {
        return $this->matcher->match($value, $this->pattern);
    }

    /**
     * @return Matcher
     */
    private function createMatcher()
    {
        $factory = new SimpleFactory();

        return $factory->createMatcher();
    }
}