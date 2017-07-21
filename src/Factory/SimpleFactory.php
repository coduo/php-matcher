<?php

namespace Coduo\PHPMatcher\Factory;

use Coduo\PHPMatcher\Factory;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;

class SimpleFactory implements Factory
{
    private $parser;

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
        $parser = $this->buildParser();
        $scalarMatchers = $this->buildScalarMatchers();
        $orMatcher = $this->buildOrMatcher($scalarMatchers);
        $jsonMatcher = $this->buildJsonObjectMatcher($scalarMatchers);
        $arrayMatcher = $this->buildArrayMatcher($scalarMatchers, $orMatcher, $jsonMatcher, $parser);

        $chainMatcher = new Matcher\ChainMatcher(array(
            $orMatcher,
            $jsonMatcher,
            $scalarMatchers,
            $arrayMatcher,
        ));

        $decoratedMatcher = new Matcher\ChainMatcher([
            $chainMatcher,
            new Matcher\JsonMatcher($chainMatcher),
            new Matcher\XmlMatcher($chainMatcher),
            new Matcher\TextMatcher($chainMatcher, $parser),
        ]);

        return $decoratedMatcher;
    }

    protected function buildArrayMatcher(
        Matcher\ValueMatcher $scalarMatchers,
        Matcher\ValueMatcher $orMatcher,
        Matcher\ValueMatcher $jsonMatcher,
        Parser $parser
    )
    {
        return new Matcher\ArrayMatcher(new Matcher\ChainMatcher([
            $orMatcher,
            $scalarMatchers,
            $jsonMatcher
        ]), $parser);
    }

    /**
     * @return Matcher\ValueMatcher
     */
    protected function buildOrMatcher(Matcher\ChainMatcher $scalarMatchers)
    {
        $chainMatcher = new Matcher\ChainMatcher([
            $scalarMatchers,
            $this->buildJsonObjectMatcher($scalarMatchers)
        ]);

        return new Matcher\OrMatcher($chainMatcher);
    }

    protected function buildJsonObjectMatcher(Matcher\ValueMatcher $scalarMatchers)
    {
        $parser = $this->buildParser();

        return new Matcher\JsonObjectMatcher($scalarMatchers, $parser);
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
        if ($this->parser) {
            return $this->parser;
        }

        return $this->parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());
    }
}
