<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use InvalidArgumentException;
use function is_float;
use function is_integer;
use function sprintf;

final class LowerThan implements PatternExpander
{
    public const NAME = 'lowerThan';

    use BacktraceBehavior;

    /**
     * @var float|int
     */
    private $boundary;

    /**
     * @var null|string
     */
    private $error;

    public function __construct($boundary)
    {
        if (!is_float($boundary) && !is_integer($boundary)) {
            throw new InvalidArgumentException(sprintf('Boundary value "%s" is not a valid number.', new StringConverter($boundary)));
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

        if (!is_float($value) && !is_integer($value)) {
            $this->error = sprintf('Value "%s" is not a valid number.', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if ($value >= $this->boundary) {
            $this->error = sprintf('Value "%s" is not lower than "%s".', new StringConverter($value), new StringConverter($this->boundary));
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

    public function getError() : ?string
    {
        return $this->error;
    }
}
