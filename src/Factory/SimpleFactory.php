<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Factory;

use Coduo\PHPMatcher\Factory;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;

class SimpleFactory implements Factory
{
    public function createMatcher() : Matcher
    {
        return new Matcher($this->buildMatchers());
    }

    protected function buildMatchers() : Matcher\ChainMatcher
    {
        $scalarMatchers = $this->buildScalarMatchers();
        $orMatcher = $this->buildOrMatcher();

        $chainMatcher = new Matcher\ChainMatcher([
            $scalarMatchers,
            $orMatcher,
            new Matcher\JsonMatcher($orMatcher),
            new Matcher\XmlMatcher($orMatcher),
            new Matcher\TextMatcher($scalarMatchers, $this->buildParser())
        ]);

        return $chainMatcher;
    }

    protected function buildOrMatcher() : Matcher\ChainMatcher
    {
        $scalarMatchers = $this->buildScalarMatchers();
        $orMatcher = new Matcher\OrMatcher($scalarMatchers);
        $arrayMatcher = new Matcher\ArrayMatcher(
            new Matcher\ChainMatcher([
                $orMatcher,
                $scalarMatchers
            ]),
            $this->buildParser()
        );

        $chainMatcher = new Matcher\ChainMatcher([
            $orMatcher,
            $arrayMatcher,
        ]);

        return $chainMatcher;
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
        ]);
    }

    protected function buildParser() : Parser
    {
        return new Parser(new Lexer(), new Parser\ExpanderInitializer());
    }
}
