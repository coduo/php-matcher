<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Factory\MatcherFactory;

final class PHPMatcher
{
    public static function match($value, $pattern, string &$error = null) : bool
    {
        $matcher = (new MatcherFactory())->createMatcher();

        if (!$matcher->match($value, $pattern)) {
            $error = $matcher->getError();
            return false;
        }

        return true;
    }

    public static function matchBacktrace($value, $pattern, Backtrace $backtrace, string &$error = null) : bool
    {
        $matcher = (new MatcherFactory())->createMatcher($backtrace);

        if (!$matcher->match($value, $pattern)) {
            $error = $matcher->getError();
            return false;
        }

        return true;
    }
}
