<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class GreaterThan implements PatternExpander
{
    const NAME = 'greaterThan';

    private $boundary;

    private $error;

    public function __construct($boundary)
    {
        if (!\is_float($boundary) && !\is_int($boundary)) {
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
        if (!\is_float($value) && !\is_int($value) && !\is_numeric($value)) {
            $this->error = \sprintf('Value "%s" is not a valid number.', new StringConverter($value));
            return false;
        }

        if ($value <= $this->boundary) {
            $this->error = \sprintf('Value "%s" is not greater than "%s".', new StringConverter($value), new StringConverter($this->boundary));
            return false;
        }

        return $value > $this->boundary;
    }

    public function getError() : ?string
    {
        return $this->error;
    }
}
