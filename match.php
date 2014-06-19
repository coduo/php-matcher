<?php

use Coduo\PHPMatcher\Matcher\ArrayMatcher;
use Coduo\PHPMatcher\Matcher\CallbackMatcher;
use Coduo\PHPMatcher\Matcher\ChainMatcher;
use Coduo\PHPMatcher\Matcher\ExpressionMatcher;
use Coduo\PHPMatcher\Matcher\JsonMatcher;
use Coduo\PHPMatcher\Matcher\NullMatcher;
use Coduo\PHPMatcher\Matcher\ScalarMatcher;
use Coduo\PHPMatcher\Matcher\TypeMatcher;
use Coduo\PHPMatcher\Matcher\WildcardMatcher;
use Coduo\PHPMatcher\Matcher;

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
     * @param  mixed   $value
     * @param  mixed   $pattern
     * @return boolean
     */
    function match($value, $pattern)
    {
        $scalarMatchers = new ChainMatcher(array(
            new CallbackMatcher(),
            new ExpressionMatcher(),
            new TypeMatcher(),
            new NullMatcher(),
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
