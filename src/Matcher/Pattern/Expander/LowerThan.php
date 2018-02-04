<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class LowerThan implements PatternExpander
{
    const NAME = 'lowerThan';

    private $boundary;

    private $error;


    public function __construct($boundary)
    {
        if (!\is_float($boundary) && !\is_integer($boundary) && !\is_double($boundary)) {
            throw new \InvalidArgumentException(\sprintf('Boundary value "%s" is not a valid number.', new StringConverter($boundary)));
        }

        $this->boundary = $boundary;
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        if (!\is_float($value) && !\is_integer($value) && !\is_double($value)) {
            $this->error = \sprintf('Value "%s" is not a valid number.', new StringConverter($value));
            return false;
        }

        if ($value >= $this->boundary) {
            $this->error = \sprintf('Value "%s" is not lower than "%s".', new StringConverter($value), new StringConverter($this->boundary));
            return false;
        }

        return $value < $this->boundary;
    }

    public function getError()
    {
        return $this->error;
    }
}
