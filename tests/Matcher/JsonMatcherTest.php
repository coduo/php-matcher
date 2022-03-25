<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\JsonMatcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class JsonMatcherTest extends TestCase
{
    private ?JsonMatcher $matcher = null;

    public function setUp() : void
    {
        $backtrace = new Backtrace\InMemoryBacktrace();
        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer($backtrace));
        $scalarMatchers = new Matcher\ChainMatcher(
            self::class,
            $backtrace,
            [
                new Matcher\CallbackMatcher($backtrace),
                new Matcher\ExpressionMatcher($backtrace),
                new Matcher\NullMatcher($backtrace),
                new Matcher\StringMatcher($backtrace, $parser),
                new Matcher\IntegerMatcher($backtrace, $parser),
                new Matcher\BooleanMatcher($backtrace, $parser),
                new Matcher\DoubleMatcher($backtrace, $parser),
                new Matcher\NumberMatcher($backtrace, $parser),
                new Matcher\ScalarMatcher($backtrace),
                new Matcher\WildcardMatcher($backtrace),
            ]
        );
        $this->matcher = new JsonMatcher(
            new Matcher\ArrayMatcher($scalarMatchers, $backtrace, $parser),
            $backtrace
        );
    }

    /**
     * @dataProvider positivePatterns
     */
    public function test_positive_can_match($pattern) : void
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
    }

    public function positivePatterns()
    {
        return [
            [\json_encode(['Norbert', 'Michał'])],
            [\json_encode(['Norbert', '@string@'])],
            [\json_encode('test')],
        ];
    }

    /**
     * @dataProvider negativePatterns
     */
    public function test_negative_can_match($pattern) : void
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    public function negativePatterns()
    {
        return [
            ['@string@'],
            ['["Norbert", '],
            ['@array@.repeat({"name": "@string@", "value": "@array@"})'],
        ];
    }

    /**
     * @dataProvider positiveMatches
     */
    public function test_positive_matches($value, $pattern) : void
    {
        $this->assertTrue($this->matcher->match($value, $pattern), (string) $this->matcher->getError());
    }

    public function positiveMatches()
    {
        return [
            [
                '{"users":["Norbert","Michał"]}',
                '{"users":["@string@","@string@"]}',
            ],
            [
                '{"users":["Norbert","Michał"]}',
                '{"users":["@string@","@...@"]}',
            ],
            [
                '{"users":["Norbert","Michał"]}',
                '{"users":["@string@",@...@]}',
            ],
            [
                '{"numbers":[1,2]}',
                '{"numbers":[@integer@, @integer@]}',
            ],
            [
                '{"foobar":[1.22, 2, "hello"]}',
                '{"foobar":[@double@, @integer@, @string@]}',
            ],
            [
                '{"entries":[{"id":1,"question":"test","answer":"test","os":"test"}]}',
                '{"entries": "@array@.repeat({
                    \"id\": \"@number@\",
                    \"question\": \"@string@\",
                    \"answer\": \"@string@\",
                    \"os\": \"@*@\"
                })"}',
            ],
            [
                '{"null":[null]}',
                '{"null":[@null@]}',
            ],
            [
                '{"null":null}',
                '{"null":@null@}',
            ],
            [
                '{"username":null,"some_data":"test"}',
                '{"username":null, "some_data": @string@}',
            ],
            [
                '{"null":null}',
                '{"null":null}',
            ],
            [
                '{"users":["Norbert","Michał",[]]}',
                '{"users":["Norbert","@string@",@...@]}',
            ],
            [
                '{"users":[{"firstName":"Norbert","lastName":"Orzechowicz","roles":["ROLE_USER", "ROLE_DEVELOPER"]}]}',
                '{"users":[{"firstName":"Norbert","lastName":"Orzechowicz","roles":"@wildcard@"}]}',
            ],
            [
                '{"users":[{"firstName":"Norbert","lastName":"Orzechowicz","roles":["ROLE_USER", "ROLE_DEVELOPER"]}]}',
                '{"users":[{"firstName":"Norbert","@*@":"@*@"}]}',
            ],
            [
                '{"users":[{"firstName":"Norbert","lastName":"Orzechowicz","roles":["ROLE_USER", "ROLE_DEVELOPER"]},{}]}',
                '{"users":[{"firstName":"Norbert","@*@":"@*@"},@...@]}',
            ],
            [
                '[{"name": "Norbert"},{"name":"Michał"},{"name":"Bob"},{"name":"Martin"}]',
                '[{"name": "Norbert"},@...@]',
            ],
            [
                '[{"name": "Norbert","lastName":"Orzechowicz"},{"name":"Michał"},{"name":"Bob"},{"name":"Martin"}]',
                '[{"name": "Norbert","@*@":"@*@"},@...@]',
            ],
            [
                '[{"name": "Norbert"},{"name":"Michał"},{"name":"Bob"},{"name":"Martin"}]',
                '"@array@.repeat({\"name\": \"@string@\"})"',
            ],
            [
                '{"something": "5e61188283825"}',
                '{"something": "@string@"}',
            ],
        ];
    }

    /**
     * @dataProvider normalizationRequiredDataProvider
     */
    public function test_positive_matches_after_normalization($value, $pattern) : void
    {
        $this->assertTrue($this->matcher->match($value, $pattern), (string) $this->matcher->getError());
    }

    public function normalizationRequiredDataProvider()
    {
        return [
            [
                '{"name": "Norbert"}',
                '{"name": @string@}',
            ],
            [
                '{"name": 25}',
                '{"name": @number@}',
            ],
            [
                '{"name": 25}',
                '{"name": @integer@}',
            ],
            [
                '{"name": true}',
                '{"name": @boolean@}',
            ],
            [
                '{"name": ["Norbert", "Michał"]}',
                '{"name": ["Norbert", @...@]}',
            ],
            [
                '{"name": "Norbert", "roles": ["ADMIN", "USER"]}',
                '{"name": @string@, "roles": [@string@, @string@]}',
            ],
        ];
    }

    /**
     * @dataProvider negativeMatches
     */
    public function test_negative_matches($value, $pattern, string $error) : void
    {
        $this->assertFalse($this->matcher->match($value, $pattern), (string) $this->matcher->getError());
        $this->assertSame($error, $this->matcher->getError());
    }

    public function negativeMatches()
    {
        return [
            [
                '{"users":["Norbert","Michał"]}',
                '{"users":["Michał","@string@"]}',
                'Value "Norbert" does not match pattern "Michał" at path: "[users][0]"',
            ],
            [
                '{"users":["Norbert","Michał", "John"], "stuff": [1, 2, 3]}',
                '{"users":["@string@", @...@], "stuff": [1, 2]}',
                'There is no element under path [stuff][2] in pattern.',
            ],
            [
                '{this_is_not_valid_json',
                '{"users":["Michał","@string@"]}',
                'Invalid given JSON of value. Syntax error, malformed JSON',
            ],
            [
                '{"status":"ok","data":[]}',
                '{"status":"ok","data":[{"id": 4,"code":"123987","name":"Anvill","short_description":"ACME Anvill","url":"http://test-store.example.com/p/123987","image":{"url":"http://test-store.example.com/i/123987-0.jpg","description":"ACME Anvill"},"price":95,"promotion_description":"Anvills sale"},{"id": 5,"code":"123988","name":"Red Anvill","short_description":"Red ACME Anvill","url":"http://test-store.example.com/p/123988","image":{"url":"http://test-store.example.com/i/123988-0.jpg","description":"ACME Anvill"},"price":44.99,"promotion_description":"Red is cheap"}]}',
                "There is no element under path [url] in value.\nThere is no element under path [id] in value.\nThere is no element under path [url] in value.\nThere is no element under path [id] in value.\nThere is no element under path [data][0] in value.",
            ],
            [
                '{"foo":"foo val","bar":"bar val"}',
                '{"foo":"foo val"}',
                'There is no element under path [bar] in pattern.',
            ],
            [
                '[{"name": "Norbert","lastName":"Orzechowicz"},{"name":"Michał"},{"name":"Bob"},{"name":"Martin"}]',
                '"@array@.repeat({\"name\": \"@string@\"})"',
                'Value "Array(4)" does not match pattern "@array@.repeat({"name": "@string@"})" at path: "root"',
            ],
            [
                [],
                '[]',
                'Invalid given JSON of value. Unknown error',
            ],
        ];
    }

    public function test_error_when_matching_fail() : void
    {
        $value = \json_encode([
            'users' => [
                ['name' => 'Norbert'],
                ['name' => 'Michał'],
            ],
        ]);
        $pattern = \json_encode([
            'users' => [
                ['name' => '@string@'],
                ['name' => '@boolean@'],
            ],
        ]);

        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertEquals('Value "Michał" does not match pattern "@boolean@" at path: "[users][1][name]"', $this->matcher->getError());
    }

    public function test_error_when_path_in_nested_pattern_does_not_exist() : void
    {
        $value = \json_encode(['foo' => ['bar' => ['baz' => 'bar value']]]);
        $pattern = \json_encode(['foo' => ['bar' => ['faz' => 'faz value']]]);

        $this->assertFalse($this->matcher->match($value, $pattern));

        $this->assertEquals('There is no element under path [foo][bar][baz] in pattern.', $this->matcher->getError());
    }

    public function test_error_when_path_in_nested_value_does_not_exist() : void
    {
        $value = \json_encode(['foo' => ['bar' => []]]);
        $pattern = \json_encode(['foo' => ['bar' => ['faz' => 'faz value']]]);

        $this->assertFalse($this->matcher->match($value, $pattern));

        $this->assertEquals('There is no element under path [foo][bar][faz] in value.', $this->matcher->getError());
    }

    public function test_error_when_json_pattern_is_invalid() : void
    {
        $value = '{"test": "value"}';
        $pattern = '{"test": "@string@",}';

        $this->assertFalse($this->matcher->match($value, $pattern));

        $this->assertEquals('Invalid given JSON of pattern. Syntax error, malformed JSON', $this->matcher->getError());
    }

    /**
     * Solves https://github.com/coduo/php-matcher/issues/156.
     */
    public function test_empty_error_after_successful_match() : void
    {
        $this->assertTrue($this->matcher->match($value = '{"foo": "bar"}', $pattern = '{"foo": "@string@"}'));
        $this->assertNull($this->matcher->getError());
    }

    public function test_error_when_json_value_is_invalid() : void
    {
        $value = '{"test": "value",}';
        $pattern = '{"test": "@string@"}';

        $this->assertFalse($this->matcher->match($value, $pattern));

        $this->assertEquals('Invalid given JSON of value. Syntax error, malformed JSON', $this->matcher->getError());
    }

    public function test_comparing_value_against_pattern_without_any_patterns() : void
    {
        $value = '{"availableLocales": ["en"]}';
        $pattern = '{"availableLocales": null}';

        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertEquals("Value \"Array(1)\" does not match pattern \"\" at path: \"[availableLocales]\"", $this->matcher->getError());
    }
}
