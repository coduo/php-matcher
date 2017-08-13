<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

final class Optional implements PatternExpander
{
    const NAME = 'optional';

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        return true;
    }

    public function getError()
    {
        return null;
    }
}
