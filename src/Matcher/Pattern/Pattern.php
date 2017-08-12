<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern;

interface Pattern
{
    public function addExpander(PatternExpander $expander);

    public function matchExpanders($value) : bool;

    public function getError();

    public function hasExpander(string $expanderName) : bool;
}
