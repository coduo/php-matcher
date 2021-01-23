<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class NotContains implements PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'notContains';

    private ?string $error = null;

    private string $string;

    private $ignoreCase;

    public function __construct(string $string, $ignoreCase = false)
    {
        $this->string = $string;
        $this->ignoreCase = $ignoreCase;
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!\is_string($value)) {
            $this->error = \sprintf('Not contains expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $contains = $this->ignoreCase
            ? \mb_stripos($value, $this->string)
            : \mb_strpos($value, $this->string);

        if ($contains !== false) {
            $this->error = \sprintf('String "%s" contains "%s".', $value, $this->string);
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
