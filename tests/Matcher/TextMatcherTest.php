<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class TextMatcherTest extends TestCase
{
    /**
     * @var Matcher\TextMatcher
     */
    private $matcher;

    public function setUp() : void
    {
        $parser = new Parser(new Lexer(), new Parser\ExpanderInitializer());
        $scalarMatchers = new Matcher\ChainMatcher(
            self::class,
            $backtrace = new Backtrace(),
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
        $this->matcher = new Matcher\TextMatcher(
            new Matcher\ChainMatcher(
                self::class,
                $backtrace,
                [
                    $scalarMatchers,
                    new Matcher\ArrayMatcher($scalarMatchers, $backtrace, $parser)
                ]
            ),
            $backtrace,
            $parser
        );
    }

    /**
     * @dataProvider matchingData
     */
    public function test_positive_matches($value, $pattern, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->matcher->match($value, $pattern));
    }

    public function test_ignore_valid_json_patterns()
    {
        $jsonPattern = \json_encode([
            'users' => [
                ['id' => '@number@', 'name' => 'Norbert'],
                ['id' => '@number@', 'name' => 'Michal']
            ]
        ]);

        $this->assertFalse($this->matcher->canMatch($jsonPattern));
    }

    public function test_ignore_valid_xml_patterns()
    {
        $xmlPattern = <<<XML
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://www.w3.org/2001/12/soap-envelope"
soap:encodingStyle="http://www.w3.org/2001/12/soap-encoding">

<soap:Body xmlns:m="http://www.example.org/stock">
  <m:GetStockPrice>
    <m:StockName>@string@</m:StockName>
    <m:StockValue>Any Value</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML;

        $this->assertFalse($this->matcher->canMatch($xmlPattern));
    }

    public function test_error_when_unsupported_type_pattern_used()
    {
        $pattern = 'lorem ipsum @null@ bla bla';
        $value = 'lorem ipsum bla bla';

        $this->assertFalse($this->matcher->match($value, $pattern));
        $this->assertSame('Type pattern "@null@" is not supported by TextMatcher.', $this->matcher->getError());
    }

    public function matchingData()
    {
        return [
            [
                'lorem ipsum lol lorem 24 dolorem',
                'lorem ipsum @string@.startsWith("lo") lorem @number@ dolorem',
                true
            ],
            [
                'lorem ipsum 24 dolorem',
                'lorem ipsum @integer@',
                false
            ],
            [
                '/users/12345/active',
                '/users/@integer@.greaterThan(0)/active',
                true
            ],
            [
                '/user/ebd1fb0e-45ae-11e8-842f-0ed5f89f718b/profile',
                '/user/@uuid@/@string@',
                true
            ],
            [
                '/user/12345/profile',
                '/user/@uuid@/@string@',
                false
            ]
        ];
    }
}
