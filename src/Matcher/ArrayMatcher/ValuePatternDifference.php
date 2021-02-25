<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\ArrayMatcher;

final class ValuePatternDifference implements Difference
{
    private string $value;

    private string $pattern;

    private string $path;

    public function __construct(string $value, string $pattern, string $path)
    {
        $this->value = $value;
        $this->pattern = $pattern;
        $this->path = $path;
    }

    public function format() : string
    {
        return "Value \"{$this->value}\" does not match pattern \"{$this->pattern}\" at path: \"{$this->path}\"";
    }
}
