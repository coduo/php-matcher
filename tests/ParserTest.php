<?php

namespace Coduo\PHPMatcher\Tests;

use Coduo\PHPMatcher\AST\Expander;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());
    }

    public function test_simple_pattern_without_expanders()
    {
        $pattern = "@type@";

        $this->assertEquals("type", $this->parser->getAST($pattern)->getType());
        $this->assertFalse($this->parser->getAST($pattern)->hasExpanders());
    }

    public function test_single_expander_without_args()
    {
        $pattern = "@type@.expander()";

        $this->assertEquals("type", $this->parser->getAST($pattern)->getType());
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertEquals('expander', $expanders[0]->getName());
        $this->assertFalse($expanders[0]->hasArguments());
    }

    public function test_single_expander_with_arguments()
    {
        $pattern = "@type@.expander('arg1', 2, 2.24, \"arg3\")";
        $this->assertEquals("type", $this->parser->getAST($pattern)->getType());
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $expectedArguments = array(
            "arg1",
            2,
            2.24,
            "arg3"
        );
        $this->assertEquals($expectedArguments, $expanders[0]->getArguments());
    }

    public function test_many_expanders()
    {
        $pattern = "@type@.expander('arg1', 2, 2.24, \"arg3\", null, false).expander1().expander(1,2,3, true, null)";
        $expanderArguments = array(
            array('arg1', 2, 2.24, 'arg3', null, false),
            array(),
            array(1, 2, 3, true, null)
        );

        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertEquals("type", $this->parser->getAST($pattern)->getType());
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
        return array(
            array(
                "@type@.expander({\"foo\":\"bar\"})",
                array(array("foo" => "bar"))
            ),
            array(
                "@type@.expander({1 : \"bar\"})",
                array(array(1 => "bar"))
            ),
            array(
                "@type@.expander({\"foo\":\"bar\"}, {\"foz\" : \"baz\"})",
                array(array("foo" => "bar"), array("foz" => "baz"))
            ),
            array(
                "@type@.expander({1 : 1})",
                array(array(1 => 1))
            ),
            array(
                "@type@.expander({1 : true})",
                array(array(1 => true))
            ),
            array(
                "@type@.expander({1 : 1}, {1 : 1})",
                array(array(1 => 1), array(1 => 1))
            ),
            array(
                "@type@.expander({1 : {\"foo\" : \"bar\"}}, {1 : 1})",
                array(array(1 => array("foo" => "bar")), array(1 => 1))
            ),
            array(
                "@type@.expander({null: \"bar\"})",
                array(array("" => "bar"))
            ),
            array(
                "@type@.expander({\"foo\": null})",
                array(array("foo" => null))
            ),
            array(
                "@type@.expander({\"foo\" : \"bar\", \"foz\" : \"baz\"})",
                array(array("foo" => "bar", "foz" => "baz"))
            ),
            array(
                "@type@.expander({\"foo\" : \"bar\", \"foo\" : \"baz\"})",
                array(array("foo" => "baz"))
            ),
            array(
                "@type@.expander({\"foo\" : \"bar\", 1 : {\"first\" : 1, \"second\" : 2}})",
                array(array("foo" => "bar", 1 => array("first" => 1, "second" => 2)))
            )
        );
    }

    public function test_expanders_that_takes_other_expanders_as_arguments()
    {
        $pattern = "@type@.expander(expander(\"test\"), expander(1))";
        $expanders = $this->parser->getAST($pattern)->getExpanders();

        $firstExpander = new Expander("expander");
        $firstExpander->addArgument("test");
        $secondExpander = new Expander("expander");
        $secondExpander->addArgument(1);

        $this->assertEquals(
            $expanders[0]->getArguments(),
            array(
                $firstExpander,
                $secondExpander
            )
        );
    }
}
