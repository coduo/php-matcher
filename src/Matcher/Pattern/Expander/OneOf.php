<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class OneOf implements PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'oneOf';

    /**
     * @var PatternExpander[]
     */
    private ?array $expanders = null;

    private ?string $error = null;

    public function __construct()
    {
        if (\func_num_args() < 2) {
            throw new \InvalidArgumentException('OneOf expander require at least two expanders.');
        }

        foreach (\func_get_args() as $argument) {
            if (!$argument instanceof PatternExpander) {
                throw new \InvalidArgumentException('OneOf expander require each argument to be a valid PatternExpander.');
            }

            $this->expanders[] = $argument;
        }
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        foreach ($this->expanders as $expander) {
            if ($expander->match($value)) {
                $this->backtrace->expanderSucceed(self::NAME, $value);

                return true;
            }
        }

        $this->error = \sprintf('Any expander available in OneOf expander does not match "%s".', new StringConverter($value));
        $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

        return false;
    }

    public function getError() : ?string
    {
        return $this->error;
    }
}
