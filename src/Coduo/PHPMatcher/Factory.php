<?php

namespace Coduo\PHPMatcher;

interface Factory
{
    /**
     * @return Matcher
     */
    public function createMatcher();
}
