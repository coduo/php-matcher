<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\ToString\StringConverter;

final class ChainMatcher extends Matcher
{
    /**
     * @var Backtrace
     */
    private $backtrace;

    /**
     * @var ValueMatcher[]
     */
    private $matchers;

    /**
     * @param Backtrace $backtrace
     * @param ValueMatcher[] $matchers
     */
    public function __construct(Backtrace $backtrace, array $matchers = [])
    {
        $this->backtrace = $backtrace;
        $this->matchers = $matchers;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        foreach ($this->matchers as $propertyMatcher) {
            if ($propertyMatcher->canMatch($pattern)) {
                if (true === $propertyMatcher->match($value, $pattern)) {
                    $this->backtrace->matcherSucceed(self::class, $value, $pattern);
                    return true;
                }

                $this->error = $propertyMatcher->getError();
            }
        }

        if (!isset($this->error)) {
            $this->error = \sprintf(
                'Any matcher from chain can\'t match value "%s" to pattern "%s"',
                new StringConverter($value),
                new StringConverter($pattern)
            );
        }

        $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

        return false;
    }

    public function canMatch($pattern) : bool
    {
        $this->backtrace->matcherCanMatch(self::class, $pattern, true);

        return true;
    }
}
