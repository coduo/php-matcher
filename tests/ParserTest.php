<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\AST\Expander;
use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private ?Parser $parser = null;

    public static function expandersWithArrayArguments()
    {
        return [
            [
                '@type@.expander({"foo":"bar"})',
                [['foo' => 'bar']],
            ],
            [
                '@type@.expander({1 : "bar"})',
                [[1 => 'bar']],
            ],
            [
                '@type@.expander({"foo":"bar"}, {"foz" : "baz"})',
                [['foo' => 'bar'], ['foz' => 'baz']],
            ],
            [
                '@type@.expander({1 : 1})',
                [[1 => 1]],
            ],
            [
                '@type@.expander({1 : true})',
                [[1 => true]],
            ],
            [
                '@type@.expander({1 : 1}, {1 : 1})',
                [[1 => 1], [1 => 1]],
            ],
            [
                '@type@.expander({1 : {"foo" : "bar"}}, {1 : 1})',
                [[1 => ['foo' => 'bar']], [1 => 1]],
            ],
            [
                '@type@.expander({null: "bar"})',
                [['' => 'bar']],
            ],
            [
                '@type@.expander({"foo": null})',
                [['foo' => null]],
            ],
            [
                '@type@.expander({"foo" : "bar", "foz" : "baz"})',
                [['foo' => 'bar', 'foz' => 'baz']],
            ],
            [
                '@type@.expander({"foo" : "bar", "foo" : "baz"})',
                [['foo' => 'baz']],
            ],
            [
                '@type@.expander({"foo" : "bar", 1 : {"first" : 1, "second" : 2}})',
                [['foo' => 'bar', 1 => ['first' => 1, 'second' => 2]]],
            ],
        ];
    }

    public function setUp() : void
    {
        $this->parser = new Parser(new Lexer(), new Parser\ExpanderInitializer(new Backtrace\InMemoryBacktrace()));
    }

    public function test_simple_pattern_without_expanders() : void
    {
        $pattern = '@type@';

        $this->assertSame('type', (string) $this->parser->getAST($pattern)->getType());
        $this->assertFalse($this->parser->getAST($pattern)->hasExpanders());
    }

    public function test_single_expander_without_args() : void
    {
        $pattern = '@type@.expander()';

        $this->assertSame('type', (string) $this->parser->getAST($pattern)->getType());
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertSame('expander', $expanders[0]->getName());
        $this->assertFalse($expanders[0]->hasArguments());
    }

    public function test_single_expander_with_arguments() : void
    {
        $pattern = "@type@.expander('arg1', 2, 2.24, \"arg3\")";
        $this->assertSame('type', (string) $this->parser->getAST($pattern)->getType());
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $expectedArguments = [
            'arg1',
            2,
            2.24,
            'arg3',
        ];
        $this->assertSame($expectedArguments, $expanders[0]->getArguments());
    }

    public function test_many_expanders() : void
    {
        $pattern = "@type@.expander('arg1', 2, 2.24, \"arg3\", null, false).expander1().expander(1,2,3, true, null)";
        $expanderArguments = [
            ['arg1', 2, 2.24, 'arg3', null, false],
            [],
            [1, 2, 3, true, null],
        ];

        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertSame('type', (string) $this->parser->getAST($pattern)->getType());
        $this->assertSame('expander', $expanders[0]->getName());
        $this->assertSame('expander1', $expanders[1]->getName());
        $this->assertSame('expander', $expanders[2]->getName());

        $this->assertSame($expanderArguments[0], $expanders[0]->getArguments());
        $this->assertSame($expanderArguments[1], $expanders[1]->getArguments());
        $this->assertSame($expanderArguments[2], $expanders[2]->getArguments());
    }

    /**
     * @dataProvider expandersWithArrayArguments
     */
    public function test_single_array_argument_with_string_key_value($pattern, $expectedArgument) : void
    {
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertSame($expectedArgument, $expanders[0]->getArguments());
    }

    public function test_expanders_that_takes_other_expanders_as_arguments() : void
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
                $secondExpander,
            ]
        );
    }
}
