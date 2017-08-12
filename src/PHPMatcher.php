<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Factory\SimpleFactory;

final class PHPMatcher
{
    public static function match($value, $pattern, string &$error = null) : bool
    {
        $factory = new SimpleFactory();
        $matcher = $factory->createMatcher();

        if (!$matcher->match($value, $pattern)) {
            $error = $matcher->getError();
            return false;
        }

        return true;
    }
}
