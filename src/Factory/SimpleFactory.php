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
        $orMatcher = $this->buildOrMatcher();

        $chainMatcher = new Matcher\ChainMatcher(array(
            $scalarMatchers,
            $orMatcher,
            new Matcher\JsonMatcher($orMatcher),
            new Matcher\XmlMatcher($orMatcher),
            new Matcher\TextMatcher($scalarMatchers, $this->buildParser())
        ));

        return $chainMatcher;
    }

    /**
     * @return Matcher\ChainMatcher
     */
    protected function buildOrMatcher()
    {
        $scalarMatchers = $this->buildScalarMatchers();
        $orMatcher = new Matcher\OrMatcher($scalarMatchers);
        $arrayMatcher = new Matcher\ArrayMatcher(
            new Matcher\ChainMatcher(array(
                $orMatcher,
                $scalarMatchers
            )),
            $this->buildParser()
        );

        $chainMatcher = new Matcher\ChainMatcher(array(
            $orMatcher,
            $arrayMatcher,
        ));

        return $chainMatcher;
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
            new Matcher\WildcardMatcher(),
            new Matcher\UuidMatcher(),
        ));
    }

    /**
     * @return Parser
     */
    protected function buildParser()
    {
        return new Parser(new Lexer(), new Parser\ExpanderInitializer());
    }
}
