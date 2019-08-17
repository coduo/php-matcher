<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Value\SingleLineString;
use Coduo\ToString\StringConverter;

final class ChainMatcher extends Matcher
{
    private $name;
    private $backtrace;
    private $matchers;

    /**
     * @param Backtrace $backtrace
     * @param ValueMatcher[] $matchers
     */
    public function __construct(string $name, Backtrace $backtrace, array $matchers = [])
    {
        $this->backtrace = $backtrace;
        $this->matchers = $matchers;
        $this->name = $name;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance($this->matcherName(), $value, $pattern);

        foreach ($this->matchers as $propertyMatcher) {
            if ($propertyMatcher->canMatch($pattern)) {
                if (true === $propertyMatcher->match($value, $pattern)) {
                    $this->backtrace->matcherSucceed($this->matcherName(), $value, $pattern);
                    return true;
                }

                $this->error = $propertyMatcher->getError();
            }
        }

        if (!isset($this->error)) {
            $this->error = \sprintf(
                'Any matcher from chain can\'t match value "%s" to pattern "%s"',
                new SingleLineString((string) new StringConverter($value)),
                new SingleLineString((string) new StringConverter($pattern))
            );
        }

        $this->backtrace->matcherFailed($this->matcherName(), $value, $pattern, $this->error);

        return false;
    }

    public function canMatch($pattern) : bool
    {
        $this->backtrace->matcherCanMatch($this->matcherName(), $pattern, true);

        return true;
    }

    /**
     * @return string
     */
    private function matcherName(): string
    {
        return \sprintf('%s (%s)', self::class, $this->name);
    }
}
