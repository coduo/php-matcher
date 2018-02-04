<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class InArray implements PatternExpander
{
    const NAME = 'inArray';

    private $error;

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        if (!\is_array($value)) {
            $this->error = \sprintf('InArray expander require "array", got "%s".', new StringConverter($value));
            return false;
        }

        if (!\in_array($this->value, $value, true)) {
            $this->error = \sprintf("%s doesn't have \"%s\" element.", new StringConverter($value), new StringConverter($this->value));
            return false;
        }

        return true;
    }

    public function getError()
    {
        return $this->error;
    }
}
