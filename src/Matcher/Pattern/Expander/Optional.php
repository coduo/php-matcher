<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

final class Optional implements PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'optional';

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);
        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    public function getError() : ?string
    {
        return null;
    }
}
