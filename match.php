<?php

use JsonMatcher\Matcher\ArrayMatcher;
use JsonMatcher\Matcher\ChainMatcher;
use JsonMatcher\Matcher\JsonMatcher;
use JsonMatcher\Matcher\ScalarMatcher;
use JsonMatcher\Matcher\TypeMatcher;
use JsonMatcher\Matcher\WildcardMatcher;
use JsonMatcher\Matcher;

if (is_dir($vendor = __DIR__ . '/../vendor')) {
    require_once($vendor . '/autoload.php');
} elseif (is_dir($vendor = __DIR__ . '/../../../vendor')) {
    require_once($vendor . '/autoload.php');
} elseif (is_dir($vendor = __DIR__ . '/vendor')) {
    require_once($vendor . '/autoload.php');
} else {
    die(
        'You must set up the project dependencies, run the following commands:' . PHP_EOL .
        'curl -s http://getcomposer.org/installer | php' . PHP_EOL .
        'php composer.phar install' . PHP_EOL
    );
}

if (!function_exists('match')) {
    /**
     * @param mixed $value
     * @param mixed $pattern
     * @return boolean
     */
    function match($value, $pattern)
    {
        $scalarMatchers = new ChainMatcher(array(
            new TypeMatcher(),
            new ScalarMatcher(),
            new WildcardMatcher()
        ));
        $arrayMatcher = new ArrayMatcher($scalarMatchers);
        $matcher = new Matcher(new ChainMatcher(array(
            $scalarMatchers,
            $arrayMatcher,
            new JsonMatcher($arrayMatcher)
        )));

        return $matcher->match($value, $pattern);
    }
}
