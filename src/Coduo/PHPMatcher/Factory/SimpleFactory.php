<?php

namespace Coduo\PHPMatcher\Factory;

use Coduo\PHPMatcher\Factory;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;

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
        $arrayMatcher = new Matcher\ArrayMatcher($scalarMatchers, $this->buildParser());

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
        $parser = $this->buildParser();

        return new Matcher\ChainMatcher(array(
            new Matcher\CallbackMatcher(),
            new Matcher\ExpressionMatcher(),
            new Matcher\NullMatcher(),
            new Matcher\StringMatcher($parser),
            new Matcher\IntegerMatcher($parser),
            new Matcher\BooleanMatcher(),
            new Matcher\DoubleMatcher($parser),
            new Matcher\NumberMatcher(),
            new Matcher\ScalarMatcher(),
            new Matcher\WildcardMatcher()
        ));
    }

    /**
     * @return Parser
     */
    protected function buildParser()
    {
        return new Parser(new Lexer());
    }
}
