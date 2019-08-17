<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Factory;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Factory;
use Coduo\PHPMatcher\Matcher;

/**
 * @deprecated Please use \Coduo\PHPMatcher\Factory\MatcherFactory instead
 */
class SimpleFactory implements Factory
{
    public function createMatcher(Backtrace $backtrace = null) : Matcher
    {
        return (new MatcherFactory())->createMatcher();
    }
}
