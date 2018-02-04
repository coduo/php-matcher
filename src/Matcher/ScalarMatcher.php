<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\StringConverter;

final class ScalarMatcher extends Matcher
{
    public function match($value, $pattern) : bool
    {
        if ($value !== $pattern) {
            $this->error = \sprintf('"%s" does not match "%s".', new StringConverter($value), new StringConverter($pattern));
            return false;
        }

        return true;
    }

    public function canMatch($pattern) : bool
    {
        return \is_scalar($pattern);
    }
}
