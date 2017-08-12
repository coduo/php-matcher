<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher;

interface Factory
{
    /**
     * @return Matcher
     */
    public function createMatcher() : Matcher;
}
