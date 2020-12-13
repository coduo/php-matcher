<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Aeon\Calendar\Gregorian\DateTime;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class After implements PatternExpander
{
    use BacktraceBehavior;

    public const NAME = 'after';

    private DateTime $boundary;

    private ?string $error;

    public function __construct($boundary)
    {
        $this->error = null;

        if (!\is_string($boundary)) {
            $this->error = \sprintf('After expander require "string", got "%s".', new StringConverter($boundary));
        }

        try {
            $this->boundary = DateTime::fromString($boundary);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(\sprintf('Boundary value "%s" is not a valid date.', new StringConverter($boundary)));
        }
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!\is_string($value)) {
            $this->error = \sprintf('After expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        try {
            $datetime = DateTime::fromString($value);

            if ($datetime->isBefore($this->boundary)) {
                $this->error = \sprintf('Value "%s" is after "%s".', new StringConverter($value), new StringConverter($this->boundary));
                $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

                return false;
            }

            $result = $datetime->isAfter($this->boundary);

            if ($result) {
                $this->backtrace->expanderSucceed(self::NAME, $value);
            } else {
                $this->backtrace->expanderFailed(self::NAME, $value, '');
            }

            return $result;
        } catch (\Exception $e) {
            $this->error = \sprintf('Value "%s" is not a valid date.', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }
    }

    public function getError() : ?string
    {
        return $this->error;
    }
}
