<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Factory;

use Coduo\PHPMatcher\Factory;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;

class SimpleFactory implements Factory
{
    private $parser;

    public function createMatcher() : Matcher
    {
        return new Matcher($this->buildMatchers());
    }

    protected function buildMatchers() : Matcher\ChainMatcher
    {
        $scalarMatchers = $this->buildScalarMatchers();
        $arrayMatcher = $this->buildArrayMatcher();

        $chainMatcher = new Matcher\ChainMatcher([
            $scalarMatchers,
            $arrayMatcher,
            new Matcher\OrMatcher($scalarMatchers),
            new Matcher\JsonMatcher($arrayMatcher),
            new Matcher\XmlMatcher($arrayMatcher),
            new Matcher\TextMatcher($scalarMatchers, $this->buildParser())
        ]);

        return $chainMatcher;
    }

    protected function buildArrayMatcher() : Matcher\ArrayMatcher
    {
        $scalarMatchers = $this->buildScalarMatchers();
        $orMatcher = new Matcher\OrMatcher($scalarMatchers);
        $arrayMatcher = new Matcher\ArrayMatcher(
            new Matcher\ChainMatcher([
                $orMatcher,
                $scalarMatchers,
                new Matcher\TextMatcher($scalarMatchers, $this->buildParser())
            ]),
            $this->buildParser()
        );


        return $arrayMatcher;
    }

    /**
     * @return Matcher\ChainMatcher
     */
    protected function buildScalarMatchers() : Matcher\ChainMatcher
    {
        $parser = $this->buildParser();

        return new Matcher\ChainMatcher([
            new Matcher\CallbackMatcher(),
            new Matcher\ExpressionMatcher(),
            new Matcher\NullMatcher(),
            new Matcher\StringMatcher($parser),
            new Matcher\IntegerMatcher($parser),
            new Matcher\BooleanMatcher($parser),
            new Matcher\DoubleMatcher($parser),
            new Matcher\NumberMatcher($parser),
            new Matcher\ScalarMatcher(),
            new Matcher\WildcardMatcher(),
            new Matcher\UuidMatcher($parser),
            new Matcher\JsonObjectMatcher($parser)
        ]);
    }

    protected function buildParser() : Parser
    {
        if ($this->parser) {
            return $this->parser;
        }

        $this->parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());

        return $this->parser;
    }
}
