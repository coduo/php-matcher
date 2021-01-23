<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Backtrace;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Value\SingleLineString;
use Coduo\ToString\StringConverter;

final class InMemoryBacktrace implements Backtrace
{
    /**
     * @var string[]
     */
    private array $trace = [];

    public function __construct()
    {
        $this->trace = [];
    }

    public function __toString() : string
    {
        return \implode("\n", $this->trace);
    }

    public function matcherCanMatch(string $name, $value, bool $result) : void
    {
        $this->trace[] = \sprintf(
            '#%d Matcher %s %s match pattern "%s"',
            $this->entriesCount(),
            $name,
            $result ? 'can' : "can't",
            new SingleLineString((string) new StringConverter($value))
        );
    }

    public function matcherEntrance(string $name, $value, $pattern) : void
    {
        $this->trace[] = \sprintf(
            '#%d Matcher %s matching value "%s" with "%s" pattern',
            $this->entriesCount(),
            $name,
            new SingleLineString((string) new StringConverter($value)),
            new SingleLineString((string) new StringConverter($pattern))
        );
    }

    public function matcherSucceed(string $name, $value, $pattern) : void
    {
        $this->trace[] = \sprintf(
            '#%d Matcher %s successfully matched value "%s" with "%s" pattern',
            $this->entriesCount(),
            $name,
            new SingleLineString((string) new StringConverter($value)),
            new SingleLineString((string) new StringConverter($pattern))
        );
    }

    public function matcherFailed(string $name, $value, $pattern, string $error) : void
    {
        $this->trace[] = \sprintf(
            '#%d Matcher %s failed to match value "%s" with "%s" pattern',
            $this->entriesCount(),
            $name,
            new SingleLineString((string) new StringConverter($value)),
            new SingleLineString((string) new StringConverter($pattern))
        );

        $this->trace[] = \sprintf(
            '#%d Matcher %s error: %s',
            $this->entriesCount(),
            $name,
            new SingleLineString($error)
        );
    }

    public function expanderEntrance(string $name, $value) : void
    {
        $this->trace[] = \sprintf(
            '#%d Expander %s matching value "%s"',
            $this->entriesCount(),
            $name,
            new SingleLineString((string) new StringConverter($value))
        );
    }

    public function expanderSucceed(string $name, $value) : void
    {
        $this->trace[] = \sprintf(
            '#%d Expander %s successfully matched value "%s"',
            $this->entriesCount(),
            $name,
            new SingleLineString((string) new StringConverter($value))
        );
    }

    public function expanderFailed(string $name, $value, string $error) : void
    {
        $this->trace[] = \sprintf(
            '#%d Expander %s failed to match value "%s"',
            $this->entriesCount(),
            $name,
            new SingleLineString((string) new StringConverter($value))
        );

        $this->trace[] = \sprintf(
            '#%d Expander %s error: %s',
            $this->entriesCount(),
            $name,
            new SingleLineString($error)
        );
    }

    public function isEmpty() : bool
    {
        return \count($this->trace) === 0;
    }

    /**
     * @return mixed[]
     */
    public function raw() : array
    {
        return $this->trace;
    }

    public function last() : ?string
    {
        if ($this->entriesCount()) {
            return \end($this->trace);
        }

        return null;
    }

    private function entriesCount() : int
    {
        return \count($this->trace) + 1;
    }
}
