<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class Count implements PatternExpander
{
    const NAME = 'count';

    private $error;

    private $value;

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function match($value) :bool
    {
        if (!\is_array($value)) {
            $this->error = \sprintf('Count expander require "array", got "%s".', new StringConverter($value));
            return false;
        }

        if (\count($value) !== $this->value) {
            $this->error = \sprintf('Expected count of %s is %s.', new StringConverter($value), new StringConverter($this->value));
            return false;
        }

        return true;
    }
    public function getError()
    {
        return $this->error;
    }
}
