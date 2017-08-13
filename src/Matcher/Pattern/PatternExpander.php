<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern;

interface PatternExpander
{
    public static function is(string $name) : bool;

    public function match($value) : bool;

    public function getError();
}
