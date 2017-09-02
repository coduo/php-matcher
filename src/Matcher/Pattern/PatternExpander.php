<?php

namespace Coduo\PHPMatcher\Matcher\Pattern;

interface PatternExpander
{
    /**
     * @param string $name
     * @return bool
     */
    public static function is($name);

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
