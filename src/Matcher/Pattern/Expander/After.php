<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Aeon\Calendar\Gregorian\DateTime;
use Aeon\Calendar\Gregorian\Time;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class After implements PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'after';

    private ?DateTime $boundaryDateTime;

    private ?Time $boundaryTime;

    private ?string $error;

    public function __construct($boundary)
    {
        $this->error = null;
        $this->boundaryTime = null;
        $this->boundaryDateTime = null;

        if (!\is_string($boundary)) {
            $this->error = \sprintf('After expander require "string", got "%s".', new StringConverter($boundary));
        }

        try {
            $this->boundaryDateTime = DateTime::fromString($boundary);
        } catch (\Exception $exception) {
            try {
                $this->boundaryTime = Time::fromString($boundary);
            } catch (\Exception $exception) {
                throw new \InvalidArgumentException(\sprintf('Boundary value "%s" is not a valid date, date time or time.', new StringConverter($boundary)));
            }
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

        if ($this->boundaryDateTime instanceof DateTime) {
            return $this->compareDateTime($value);
        }

        return $this->compareTime($value);
    }

    public function getError() : ?string
    {
        return $this->error;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function compareDateTime(string $value) : bool
    {
        try {
            $datetime = DateTime::fromString($value);

            if ($datetime->isBefore($this->boundaryDateTime)) {
                $this->error = \sprintf('Value "%s" is after "%s".', new StringConverter($value), new StringConverter($this->boundaryDateTime));
                $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

                return false;
            }

            $result = $datetime->isAfter($this->boundaryDateTime);

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

    /**
     * @param string $value
     *
     * @return bool
     */
    private function compareTime(string $value) : bool
    {
        try {
            $datetime = Time::fromString($value);

            if ($datetime->isLessThan($this->boundaryTime)) {
                $this->error = \sprintf('Value "%s" is after "%s".', new StringConverter($value), new StringConverter($this->boundaryTime));
                $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

                return false;
            }

            $result = $datetime->isGreaterThan($this->boundaryTime);

            if ($result) {
                $this->backtrace->expanderSucceed(self::NAME, $value);
            } else {
                $this->backtrace->expanderFailed(self::NAME, $value, '');
            }

            return $result;
        } catch (\Exception $e) {
            $this->error = \sprintf('Value "%s" is not a valid time.', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }
    }
}
