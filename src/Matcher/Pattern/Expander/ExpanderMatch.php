<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Factory\MatcherFactory;
use Coduo\PHPMatcher\Matcher;

final class ExpanderMatch implements Matcher\Pattern\PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'match';

    private ?Matcher $matcher = null;

    /**
     * @var null|array|string
     */
    private $pattern;

    /**
     * @param null|array|string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    /**
     * @param mixed $value
     */
    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if ($this->matcher === null) {
            $this->matcher = (new MatcherFactory())->createMatcher($this->backtrace);
        }

        $result = $this->matcher->match($value, $this->pattern);

        if ($result) {
            $this->backtrace->expanderSucceed(self::NAME, $value);
        } else {
            $this->backtrace->expanderFailed(self::NAME, $value, '');
        }

        return $result;
    }

    public function getError() : ?string
    {
        return $this->matcher->getError();
    }
}
