<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class XmlMatcherTest extends TestCase
{
    private ?Matcher\XmlMatcher $matcher = null;

    public static function positivePatterns()
    {
        return [
            ['<xml></xml>'],
            ['<users><user>@string@</user></users>'],
        ];
    }

    public static function negativePatterns()
    {
        return [
            ['<xml '],
            ['asdkasdasdqwrq'],
        ];
    }

    public static function positiveMatches()
    {
        return [
            [
                '<users><user>Norbert</user><user>Michał</user></users>',
                '<users><user>@string@</user><user>@string@</user></users>',
            ],
            [
                '<users><user id="1">Norbert</user></users>',
                '<users><user id="@string@">@string@</user></users>',
            ],
            [
                '<users><user><name>Norbert</name><age>25</age></user></users>',
                '<users><user><name>Norbert</name><age>@string@</age></user></users>',
            ],
            [
                '<string><![CDATA[Any kind of text here]]></string>',
                '<string><![CDATA[@string@]]></string>',
            ],
            [
                <<<'XML'
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://www.w3.org/2001/12/soap-envelope"
soap:encodingStyle="http://www.w3.org/2001/12/soap-encoding">

<soap:Body xmlns:m="http://www.example.org/stock">
  <m:GetStockPrice>
    <m:StockName>IBM</m:StockName>
    <m:StockValue>Any Value</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML
                ,
                <<<'XML'
<?xml version="1.0"?>
<soap:Envelope
    xmlns:soap="@string@"
            soap:encodingStyle="@string@">

<soap:Body xmlns:m="@string@">
  <m:GetStockPrice>
    <m:StockName>@string@</m:StockName>
    <m:StockValue>@string@</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML
            ],
        ];
    }

    public static function negativeMatches()
    {
        return [
            [
                '<users><user>Norbert</user><user>Michał</user></users>',
                '{"users":["Michał","@string@"]}',
            ],
            [
                '<users><user>Norbert</user><user>Michał</user></users>',
                '<users><user>@integer@</user><user>@integer@</user></users>',
            ],
        ];
    }

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

        $this->matcher = new Matcher\XmlMatcher(
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

    /**
     * @dataProvider negativePatterns
     */
    public function test_negative_can_match($pattern) : void
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatches
     */
    public function test_positive_matches($value, $pattern) : void
    {
        $this->assertTrue($this->matcher->match($value, $pattern), (string) $this->matcher->getError());
    }

    /**
     * @dataProvider negativeMatches
     */
    public function test_negative_matches($value, $pattern) : void
    {
        $this->assertFalse($this->matcher->match($value, $pattern), (string) $this->matcher->getError());
    }
}
