<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern;

interface PatternExpander
{
    /**
     * @param string $name
     * @return bool
     */
    public static function is(string $name);

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
