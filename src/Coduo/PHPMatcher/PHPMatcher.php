<?php

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Factory\SimpleFactory;

final class PHPMatcher
{
    /**
     * @var Matcher|null
     */
    private static $matcher;

    /**
     * @param $value
     * @param $pattern
     * @return bool
     */
    public static function match($value, $pattern)
    {
        $matcher = self::createMatcher();
     
        return $matcher->match($value, $pattern);
    }

    /**
     * @return null|string
     */
    public static function getError()
    {
        $matcher = self::createMatcher();
        
        return $matcher->getError();
    }
    
    private static function createMatcher()
    {
        if (self::$matcher instanceof Matcher) {
            return self::$matcher;
        }
        
        $factory = new SimpleFactory();
        self::$matcher = $factory->createMatcher();
        
        return self::$matcher;
    }
}