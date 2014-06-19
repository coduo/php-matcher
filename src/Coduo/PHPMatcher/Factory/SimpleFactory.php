<?php

namespace Coduo\PHPMatcher\Factory;

use Coduo\PHPMatcher\Factory;
use Coduo\PHPMatcher\Matcher;

class SimpleFactory implements Factory
{
    /**
     * @return Matcher
     */
    public function createMatcher()
    {
        return new Matcher($this->buildMatchers());
    }

    /**
     * @return Matcher\ChainMatcher
     */
    protected function buildMatchers()
    {
        $scalarMatchers = $this->buildScalarMatchers();
        $arrayMatcher = new Matcher\ArrayMatcher($scalarMatchers);

        return new Matcher\ChainMatcher(array(
            $scalarMatchers,
            $arrayMatcher,
            new Matcher\JsonMatcher($arrayMatcher)
        ));
    }

    /**
     * @return Matcher\ChainMatcher
     */
    protected function buildScalarMatchers()
    {
        return new Matcher\ChainMatcher(array(
            new Matcher\CallbackMatcher(),
            new Matcher\ExpressionMatcher(),
            new Matcher\TypeMatcher(),
            new Matcher\NullMatcher(),
            new Matcher\ScalarMatcher(),
            new Matcher\WildcardMatcher()
        ));
    }
}
