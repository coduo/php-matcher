<?php

use Coduo\PHPMatcher\Factory\SimpleFactory;

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

/**
 * @deprecated since 1.1, to be removed in 2.0. Use SimpleFactory and object approach instead
 */
if (!function_exists('match')) {
    /**
     * @param  mixed   $value
     * @param  mixed   $pattern
     * @return boolean
     */
    function match($value, $pattern)
    {
        $factory = new SimpleFactory();
        $matcher = $factory->createMatcher();

        return $matcher->match($value, $pattern);
    }
}
