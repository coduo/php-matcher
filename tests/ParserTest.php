<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\AST\Expander;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\Modifier\CaseInsensitive;
use Coduo\PHPMatcher\Matcher\Modifier\IgnoreExtraKeys;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser(new Lexer(), new Parser\ExpanderInitializer(), new Parser\ModifiersRegistry());
    }

    public function test_simple_pattern_without_expanders()
    {
        $pattern = '@type@';

        $this->assertEquals('type', $this->parser->getAST($pattern)->getType());
        $this->assertFalse($this->parser->getAST($pattern)->hasExpanders());
    }

    public function test_single_expander_without_args()
    {
        $pattern = '@type@.expander()';

        $this->assertEquals('type', $this->parser->getAST($pattern)->getType());
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertEquals('expander', $expanders[0]->getName());
        $this->assertFalse($expanders[0]->hasArguments());
    }

    public function test_single_expander_with_arguments()
    {
        $pattern = "@type@.expander('arg1', 2, 2.24, \"arg3\")";
        $this->assertEquals('type', $this->parser->getAST($pattern)->getType());
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $expectedArguments = [
            'arg1',
            2,
            2.24,
            'arg3'
        ];
        $this->assertEquals($expectedArguments, $expanders[0]->getArguments());
    }

    public function test_many_expanders()
    {
        $pattern = "@type@.expander('arg1', 2, 2.24, \"arg3\", null, false).expander1().expander(1,2,3, true, null)";
        $expanderArguments = [
            ['arg1', 2, 2.24, 'arg3', null, false],
            [],
            [1, 2, 3, true, null]
        ];

        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertEquals('type', $this->parser->getAST($pattern)->getType());
        $this->assertEquals('expander', $expanders[0]->getName());
        $this->assertEquals('expander1', $expanders[1]->getName());
        $this->assertEquals('expander', $expanders[2]->getName());

        $this->assertEquals($expanderArguments[0], $expanders[0]->getArguments());
        $this->assertEquals($expanderArguments[1], $expanders[1]->getArguments());
        $this->assertEquals($expanderArguments[2], $expanders[2]->getArguments());
    }

    /**
     * @dataProvider expandersWithArrayArguments
     */
    public function test_single_array_argument_with_string_key_value($pattern, $expectedArgument)
    {
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertEquals($expectedArgument, $expanders[0]->getArguments());
    }

    public static function expandersWithArrayArguments()
    {
        return [
            [
                '@type@.expander({"foo":"bar"})',
                [['foo' => 'bar']]
            ],
            [
                '@type@.expander({1 : "bar"})',
                [[1 => 'bar']]
            ],
            [
                '@type@.expander({"foo":"bar"}, {"foz" : "baz"})',
                [['foo' => 'bar'], ['foz' => 'baz']]
            ],
            [
                '@type@.expander({1 : 1})',
                [[1 => 1]]
            ],
            [
                '@type@.expander({1 : true})',
                [[1 => true]]
            ],
            [
                '@type@.expander({1 : 1}, {1 : 1})',
                [[1 => 1], [1 => 1]]
            ],
            [
                '@type@.expander({1 : {"foo" : "bar"}}, {1 : 1})',
                [[1 => ['foo' => 'bar']], [1 => 1]]
            ],
            [
                '@type@.expander({null: "bar"})',
                [['' => 'bar']]
            ],
            [
                '@type@.expander({"foo": null})',
                [['foo' => null]]
            ],
            [
                '@type@.expander({"foo" : "bar", "foz" : "baz"})',
                [['foo' => 'bar', 'foz' => 'baz']]
            ],
            [
                '@type@.expander({"foo" : "bar", "foo" : "baz"})',
                [['foo' => 'baz']]
            ],
            [
                '@type@.expander({"foo" : "bar", 1 : {"first" : 1, "second" : 2}})',
                [['foo' => 'bar', 1 => ['first' => 1, 'second' => 2]]]
            ]
        ];
    }

    public function test_expanders_that_takes_other_expanders_as_arguments()
    {
        $pattern = '@type@.expander(expander("test"), expander(1))';
        $expanders = $this->parser->getAST($pattern)->getExpanders();

        $firstExpander = new Expander('expander');
        $firstExpander->addArgument('test');
        $secondExpander = new Expander('expander');
        $secondExpander->addArgument(1);

        $this->assertEquals(
            $expanders[0]->getArguments(),
            [
                $firstExpander,
                $secondExpander
            ]
        );
    }

    /**
     * @dataProvider trimmablePatterns
     */
    public function test_trims_modifiers_from_given_pattern(string $pattern, string $expected)
    {
        $trimmedPattern = $this->parser->trimModifiers($pattern);
        $this->assertEquals($expected, $trimmedPattern);
    }

    public function trimmablePatterns(): array
    {
        return [
            [
                '|case_insensitive|{id: 12345, name:"Norbert Orzechowicz"}',
                '{id: 12345, name:"Norbert Orzechowicz"}'
            ],
            [
                '|case_insensitive,ignore_extra_keys|{id: 12345, name:"Norbert Orzechowicz"}',
                '{id: 12345, name:"Norbert Orzechowicz"}'
            ]
        ];
    }

    /**
     * @dataProvider parseablePatterns
     */
    public function test_parse_modifiers_from_given_pattern(string $pattern, array $expected)
    {
        $modifiers = $this->parser->parseModifiers($pattern);
        $this->assertEquals($expected, $modifiers);
    }

    public function parseablePatterns(): array
    {
        return [
            [
                '|case_insensitive|{id: 12345, name:"Norbert Orzechowicz"}',
                [
                    new CaseInsensitive()
                ]
            ],
            [
                '|case_insensitive,ignore_extra_keys|{id: 12345, name:"Norbert Orzechowicz"}',
                [
                    new CaseInsensitive(),
                    new IgnoreExtraKeys()
                ]
            ]
        ];
    }
}
