<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher;

interface Factory
{
    public function createMatcher() : Matcher;
}
