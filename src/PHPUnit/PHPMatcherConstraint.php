<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\PHPUnit;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Coduo\PHPMatcher\Matcher;
use PHPUnit\Framework\Constraint\Constraint;

final class PHPMatcherConstraint extends Constraint
{
    private $pattern;

    private $matcher;

    public function __construct(string $pattern)
    {
        parent::__construct();

        $this->pattern = $pattern;
        $this->matcher = $this->createMatcher();
    }

    public function toString() : string
    {
        return 'matches the pattern';
    }

    protected function additionalFailureDescription($other) : string
    {
        return $this->matcher->getError();
    }

    protected function matches($value) : bool
    {
        return $this->matcher->match($value, $this->pattern);
    }

    private function createMatcher() : Matcher
    {
        $factory = new SimpleFactory();

        return $factory->createMatcher();
    }
}
