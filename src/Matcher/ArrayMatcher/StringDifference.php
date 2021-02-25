<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\ArrayMatcher;

final class StringDifference implements Difference
{
    private string $description;

    public function __construct(string $description)
    {
        $this->description = $description;
    }

    public function format() : string
    {
        return $this->description;
    }
}
