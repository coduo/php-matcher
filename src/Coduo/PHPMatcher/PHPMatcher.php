<?php

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Factory\SimpleFactory;

final class PHPMatcher
{
    /**
     * @param $value
     * @param $pattern
     * @param null $error
     * @return bool
     */
    public static function match($value, $pattern, &$error = null)
    {
        $matcher = (new SimpleFactory())->createMatcher();
     
        if (!$matcher->match($value, $pattern)) {
            $error = $matcher->getError();
            return false;
        }
        
        return true;
    }
}