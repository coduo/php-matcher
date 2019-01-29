<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class Before implements PatternExpander
{
    const NAME = 'before';

    private $boundary;

    private $error;


    public function __construct($boundary)
    {
        if (!\is_string($boundary)) {
            throw new \InvalidArgumentException(\sprintf('Before expander require "string", got "%s".', new StringConverter($boundary)));
        }

        if (!$this->is_datetime($boundary)) {
            throw new \InvalidArgumentException(\sprintf('Boundary value "%s" is not a valid date.', new StringConverter($boundary)));
        }

        $this->boundary = new \DateTime($boundary);
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        if (!\is_string($value)) {
            $this->error = \sprintf('Before expander require "string", got "%s".', new StringConverter($value));
            return false;
        }

        if (!$this->is_datetime($value)) {
            $this->error = \sprintf('Value "%s" is not a valid date.', new StringConverter($value));
            return false;
        }

        $value = new \DateTime($value);

        if ($value >= $this->boundary) {
            $this->error = \sprintf('Value "%s" is before "%s".', new StringConverter($value), new StringConverter($this->boundary));
            return false;
        }

        return $value < $this->boundary;
    }

    private function is_datetime(string $value) : bool
    {
        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }
}
