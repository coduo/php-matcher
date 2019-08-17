<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class After implements PatternExpander
{
    const NAME = 'after';

    use BacktraceBehavior;

    private $boundary;
    private $error;

    public function __construct($boundary)
    {
        if (false === \is_string($boundary)) {
            $this->error = \sprintf('After expander require "string", got "%s".', new StringConverter($boundary));
            return false;
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
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (false === \is_string($value)) {
            $this->error = \sprintf('After expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (!$this->is_datetime($value)) {
            $this->error = \sprintf('Value "%s" is not a valid date.', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $value = new \DateTime($value);

        if ($value <= $this->boundary) {
            $this->error = \sprintf('Value "%s" is not after "%s".', new StringConverter($value), new StringConverter($this->boundary));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $result = $value > $this->boundary;

        if ($result) {
            $this->backtrace->expanderSucceed(self::NAME, $value);
        } else {
            $this->backtrace->expanderFailed(self::NAME, $value, '');
        }

        return $result;
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

    public function getError() : ?string
    {
        return $this->error;
    }
}
