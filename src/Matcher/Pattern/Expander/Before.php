<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use InvalidArgumentException;
use Exception;
use DateTime;
use function is_string;
use function sprintf;

final class Before implements PatternExpander
{
    public const NAME = 'before';

    use BacktraceBehavior;

    /**
     * @var DateTime
     */
    private $boundary;

    /**
     * @var null|string
     */
    private $error;

    public function __construct($boundary)
    {
        if (!is_string($boundary)) {
            throw new InvalidArgumentException(sprintf('Before expander require "string", got "%s".', new StringConverter($boundary)));
        }

        if (!$this->is_datetime($boundary)) {
            throw new InvalidArgumentException(sprintf('Boundary value "%s" is not a valid date.', new StringConverter($boundary)));
        }

        $this->boundary = new DateTime($boundary);
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!is_string($value)) {
            $this->error = sprintf('Before expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (!$this->is_datetime($value)) {
            $this->error = sprintf('Value "%s" is not a valid date.', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $value = new DateTime($value);

        if ($value >= $this->boundary) {
            $this->error = sprintf('Value "%s" is before "%s".', new StringConverter($value), new StringConverter($this->boundary));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $result = $value < $this->boundary;

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
            new DateTime($value);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getError() : ?string
    {
        return $this->error;
    }
}
