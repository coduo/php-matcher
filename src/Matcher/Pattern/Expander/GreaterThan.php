<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class GreaterThan implements PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'greaterThan';

    /**
     * @var float|int
     */
    private $boundary;

    private ?string $error = null;

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
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!\is_float($value) && !\is_int($value) && !\is_numeric($value)) {
            $this->error = \sprintf('Value "%s" is not a valid number.', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if ($value <= $this->boundary) {
            $this->error = \sprintf('Value "%s" is not greater than "%s".', new StringConverter($value), new StringConverter($this->boundary));
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

    public function getError() : ?string
    {
        return $this->error;
    }
}
