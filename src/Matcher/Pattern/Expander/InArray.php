<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class InArray implements PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'inArray';

    private ?string $error = null;

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!\is_array($value)) {
            $this->error = \sprintf('InArray expander require "array", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (!\in_array($this->value, $value, true)) {
            $this->error = \sprintf("%s doesn't have \"%s\" element.", new StringConverter($value), new StringConverter($this->value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    public function getError() : ?string
    {
        return $this->error;
    }
}
