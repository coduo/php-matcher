<?php

namespace Coduo\PHPMatcher\Matcher\Pattern;

interface PatternExpander
{
    /**
     * @param $value
     * @return boolean
     */
    public function match($value);

    /**
     * @return string|null
     */
    public function getError();
}
