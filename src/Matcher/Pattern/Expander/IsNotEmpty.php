<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class IsNotEmpty implements PatternExpander
{
    const NAME = 'isNotEmpty';

    private $error;

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        if (false === $value || (empty($value) && '0' != $value)) {
            $this->error = \sprintf('Value %s is not blank.', new StringConverter($value));
            return false;
        }

        return true;
    }

    public function getError()
    {
        return $this->error;
    }
}
