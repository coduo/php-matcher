<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Value\SingleLineString;
use Coduo\ToString\StringConverter;

final class Backtrace
{
    private $trace;

    public function __construct()
    {
        $this->trace = [];
    }

    public function matcherCanMatch(string $matcherClass, $value, bool $result) : void
    {
        $this->trace[] = \sprintf(
            '#%d Matcher %s %s match pattern "%s"',
            $this->entriesCount(),
            $matcherClass,
            $result ? 'can' : 'can\'t',
            new SingleLineString((string) new StringConverter($value))
        );
    }

    public function matcherEntrance(string $matcherClass, $value, $pattern) : void
    {
        $this->trace[] = \sprintf(
            '#%d Matcher %s matching value "%s" with "%s" pattern',
            $this->entriesCount(),
            $matcherClass,
            new SingleLineString((string) new StringConverter($value)),
            new SingleLineString((string) new StringConverter($pattern))
        );
    }

    public function matcherSucceed(string $matcherClass, $value, $pattern) : void
    {
        $this->trace[] = \sprintf(
            '#%d Matcher %s successfully matched value "%s" with "%s" pattern',
            $this->entriesCount(),
            $matcherClass,
            new SingleLineString((string) new StringConverter($value)),
            new SingleLineString((string) new StringConverter($pattern))
        );
    }

    public function matcherFailed(string $matcherClass, $value, $pattern, string $error) : void
    {
        $this->trace[] = \sprintf(
            '#%d Matcher %s failed to match value "%s" with "%s" pattern',
            $this->entriesCount(),
            $matcherClass,
            new SingleLineString((string) new StringConverter($value)),
            new SingleLineString((string) new StringConverter($pattern))
        );

        $this->trace[] = \sprintf(
            '#%d Matcher %s error: %s',
            $this->entriesCount(),
            $matcherClass,
            new SingleLineString($error)
        );
    }

    public function __toString() : string
    {
        return \implode("\n", $this->trace);
    }

    public function raw() : array
    {
        return $this->trace;
    }

    private function entriesCount(): int
    {
        return \count($this->trace) + 1;
    }
}
