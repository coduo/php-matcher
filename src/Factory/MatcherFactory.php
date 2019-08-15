<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Factory;

use Coduo\PHPMatcher\Factory;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;

final class MatcherFactory implements Factory
{
    public function createMatcher() : Matcher
    {
        return new Matcher($this->buildMatchers($this->buildParser()));
    }

    protected function buildMatchers(Parser $parser) : Matcher\ChainMatcher
    {
        $scalarMatchers = $this->buildScalarMatchers($parser);
        $arrayMatcher = $this->buildArrayMatcher($scalarMatchers, $parser);

        // Matchers are registered in order of matching
        // 1) all scalars
        // 2) json/xml
        // 3) array
        // 4) or "||"
        // 5) full text

        $chainMatcher = new Matcher\ChainMatcher([
            $scalarMatchers,
            new Matcher\JsonMatcher($arrayMatcher),
            new Matcher\XmlMatcher($arrayMatcher),
            $arrayMatcher,
            new Matcher\OrMatcher($scalarMatchers),
            new Matcher\TextMatcher($scalarMatchers, $parser),
        ]);

        return $chainMatcher;
    }

    protected function buildArrayMatcher(Matcher\ChainMatcher $scalarMatchers, Parser $parser) : Matcher\ArrayMatcher
    {
        $orMatcher = new Matcher\OrMatcher($scalarMatchers);

        return new Matcher\ArrayMatcher(
            new Matcher\ChainMatcher([
                $orMatcher,
                $scalarMatchers,
                new Matcher\TextMatcher($scalarMatchers, $parser)
            ]),
            $parser
        );
    }

    protected function buildScalarMatchers(Parser $parser) : Matcher\ChainMatcher
    {
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
        return new Parser(new Lexer(), new Parser\ExpanderInitializer());
    }
}
